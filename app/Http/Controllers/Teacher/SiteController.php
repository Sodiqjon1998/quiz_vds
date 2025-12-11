<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\Option;
use App\Models\Question;
use App\Models\Teacher\Quiz;
use App\Models\Users;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $subjectId = $teacher->subject_id;

        // Filter parametrlari
        $startDate = $request->input('start_date', Carbon::now()->subMonths(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $classId = $request->input('class_id', null);

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // ==========================================
        // 1. ASOSIY KPI KARTALAR
        // ==========================================

        // Jami testlar soni
        $totalQuizzes = Quiz::where('subject_id', $subjectId)
            ->where('created_by', $teacher->id)
            ->count();

        // Jami savollar soni
        $totalQuestions = Question::whereIn('quiz_id', function ($query) use ($subjectId, $teacher) {
            $query->select('id')
                ->from('quiz')
                ->where('subject_id', $subjectId)
                ->where('created_by', $teacher->id);
        })->count();

        // Tanlangan davrdagi imtihonlar
        $totalExams = Exam::where('subject_id', $subjectId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->when($classId, function ($q) use ($classId) {
                return $q->whereHas('user', function ($query) use ($classId) {
                    $query->where('classes_id', $classId);
                });
            })
            ->count();

        // Unikal o'quvchilar soni
        $uniqueStudents = Exam::where('subject_id', $subjectId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->when($classId, function ($q) use ($classId) {
                return $q->whereHas('user', function ($query) use ($classId) {
                    $query->where('classes_id', $classId);
                });
            })
            ->distinct('user_id')
            ->count('user_id');

        // O'rtacha muvaffaqiyat foizi
        $examIds = Exam::where('subject_id', $subjectId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->when($classId, function ($q) use ($classId) {
                return $q->whereHas('user', function ($query) use ($classId) {
                    $query->where('classes_id', $classId);
                });
            })
            ->pluck('id');

        $totalAnswers = ExamAnswer::whereIn('exam_id', $examIds)->count();
        $correctAnswers = ExamAnswer::whereIn('exam_id', $examIds)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('option')
                    ->whereColumn('option.id', 'exam_answer.option_id')
                    ->where('option.is_correct', 1);
            })
            ->count();

        $averageScore = $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 1) : 0;

        // ==========================================
        // 2. OYLIK TREND (Oxirgi 6 oy)
        // ==========================================

        $monthlyTrend = [];
        $monthLabels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $examCount = Exam::where('subject_id', $subjectId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->when($classId, function ($q) use ($classId) {
                    return $q->whereHas('user', function ($query) use ($classId) {
                        $query->where('classes_id', $classId);
                    });
                })
                ->count();

            $monthlyTrend[] = $examCount;
            $monthLabels[] = $month->translatedFormat('M Y');
        }

        // ==========================================
        // 3. SINFLAR BO'YICHA PERFORMANS
        // ==========================================

        $classesQuery = Classes::where('status', 1);
        if ($classId) {
            $classesQuery->where('id', $classId);
        }
        $allClasses = $classesQuery->get();

        $classPerformance = [];
        foreach ($allClasses as $class) {
            // O'sha sinfdagi o'quvchilarni topish
            $studentIds = Users::where('classes_id', $class->id)
                ->where('user_type', Users::TYPE_STUDENT)
                ->where('status', 1)
                ->pluck('id');

            if ($studentIds->isEmpty()) {
                continue; // O'quvchi bo'lmasa o'tkazib yuboramiz
            }

            // O'sha sinf o'quvchilarining imtihonlari
            $classExamIds = Exam::where('subject_id', $subjectId)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->whereIn('user_id', $studentIds)
                ->pluck('id');

            if ($classExamIds->isEmpty()) {
                continue; // Imtihon bo'lmasa o'tkazib yuboramiz
            }

            // Umumiy javoblar va to'g'ri javoblar
            $classTotal = ExamAnswer::whereIn('exam_id', $classExamIds)->count();

            $classCorrect = ExamAnswer::whereIn('exam_id', $classExamIds)
                ->whereIn('option_id', function ($query) {
                    $query->select('id')
                        ->from('option')
                        ->where('is_correct', 1);
                })
                ->count();

            $percentage = $classTotal > 0 ? round(($classCorrect / $classTotal) * 100, 1) : 0;

            // Faqat ma'lumot bor bo'lsagina qo'shamiz
            if ($classTotal > 0) {
                $classPerformance[] = [
                    'name' => $class->name,
                    'percentage' => $percentage,
                    'total_exams' => $classExamIds->count(),
                    'total_answers' => $classTotal,
                    'correct_answers' => $classCorrect,
                    'students' => $studentIds->count()
                ];
            }
        }

        // ==========================================
        // 4. TOP 10 ENG QIYIN SAVOLLAR
        // ==========================================

        $difficultQuestions = Question::select('question.id', 'question.name', 'quiz.name as quiz_name')
            ->join('quiz', 'question.quiz_id', '=', 'quiz.id')
            ->where('quiz.subject_id', $subjectId)
            ->where('quiz.created_by', $teacher->id)
            ->withCount([
                'examAnswers as total_attempts' => function ($query) use ($examIds) {
                    $query->whereIn('exam_id', $examIds);
                },
                'examAnswers as wrong_attempts' => function ($query) use ($examIds) {
                    $query->whereIn('exam_id', $examIds)
                        ->whereNotExists(function ($q) {
                            $q->select(DB::raw(1))
                                ->from('option')
                                ->whereColumn('option.id', 'exam_answer.option_id')
                                ->where('option.is_correct', 1);
                        });
                }
            ])
            ->having('total_attempts', '>', 0)
            ->get()
            ->map(function ($q) {
                $q->error_rate = $q->total_attempts > 0
                    ? round(($q->wrong_attempts / $q->total_attempts) * 100, 1)
                    : 0;
                return $q;
            })
            ->sortByDesc('error_rate')
            ->take(10)
            ->values();

        // ==========================================
        // 5. TOP 10 FAOL O'QUVCHILAR
        // ==========================================

        $topStudents = Users::select('users.*')
            ->selectRaw('COUNT(exam.id) as exam_count')
            ->join('exam', 'users.id', '=', 'exam.user_id')
            ->where('exam.subject_id', $subjectId)
            ->whereBetween('exam.created_at', [$startDateTime, $endDateTime])
            ->when($classId, function ($q) use ($classId) {
                return $q->where('users.classes_id', $classId);
            })
            ->where('users.user_type', Users::TYPE_STUDENT)
            ->where('users.status', 1)
            ->groupBy('users.id')
            ->orderByDesc('exam_count')
            ->take(10)
            ->with('classRelation')
            ->get()
            ->map(function ($student) use ($subjectId, $startDateTime, $endDateTime) {
                $studentExamIds = Exam::where('user_id', $student->id)
                    ->where('subject_id', $subjectId)
                    ->whereBetween('created_at', [$startDateTime, $endDateTime])
                    ->pluck('id');

                $studentTotal = ExamAnswer::whereIn('exam_id', $studentExamIds)->count();
                $studentCorrect = ExamAnswer::whereIn('exam_id', $studentExamIds)
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('option')
                            ->whereColumn('option.id', 'exam_answer.option_id')
                            ->where('option.is_correct', 1);
                    })
                    ->count();

                return [
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'class' => $student->classRelation->name ?? 'N/A',
                    'exam_count' => $student->exam_count,
                    'accuracy' => $studentTotal > 0 ? round(($studentCorrect / $studentTotal) * 100, 1) : 0
                ];
            });

        // ==========================================
        // 6. HAFTALIK AKTIVLIK HEATMAP
        // ==========================================

        $weeklyActivity = [];
        $days = ['Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba', 'Yakshanba'];

        foreach ($days as $index => $day) {
            $dayNumber = $index + 1;
            $count = Exam::where('subject_id', $subjectId)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->whereRaw('DAYOFWEEK(created_at) = ?', [$dayNumber])
                ->when($classId, function ($q) use ($classId) {
                    return $q->whereHas('user', function ($query) use ($classId) {
                        $query->where('classes_id', $classId);
                    });
                })
                ->count();

            $weeklyActivity[] = [
                'day' => $day,
                'count' => $count
            ];
        }

        // ==========================================
        // 7. TESTLAR BO'YICHA STATISTIKA
        // ==========================================

        $quizStats = Quiz::select('quiz.id', 'quiz.name')
            ->where('quiz.subject_id', $subjectId)
            ->where('quiz.created_by', $teacher->id)
            ->withCount([
                'exams as attempt_count' => function ($query) use ($startDateTime, $endDateTime, $classId) {
                    $query->whereBetween('created_at', [$startDateTime, $endDateTime])
                        ->when($classId, function ($q) use ($classId) {
                            return $q->whereHas('user', function ($query) use ($classId) {
                                $query->where('classes_id', $classId);
                            });
                        });
                }
            ])
            ->having('attempt_count', '>', 0)
            ->orderByDesc('attempt_count')
            ->take(10)
            ->get();

        // Filter uchun sinflar ro'yxati
        $filterClasses = Classes::where('status', 1)->orderBy('name')->get();

        return view('teacher.site.index', compact(
            'totalQuizzes',
            'totalQuestions',
            'totalExams',
            'uniqueStudents',
            'averageScore',
            'monthlyTrend',
            'monthLabels',
            'classPerformance',
            'difficultQuestions',
            'topStudents',
            'weeklyActivity',
            'quizStats',
            'filterClasses',
            'startDate',
            'endDate',
            'classId'
        ));
    }
}
