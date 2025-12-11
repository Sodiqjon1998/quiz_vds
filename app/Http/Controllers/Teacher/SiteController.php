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
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str; // Str::limit funksiyasi uchun

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $subjectId = $teacher->subject_id;

        // Filter parametrlari. Default: Oxirgi 6 oy
        $defaultStartDate = Carbon::now()->subMonths(6)->format('Y-m-d');
        $defaultEndDate = Carbon::now()->format('Y-m-d');

        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);
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

        // Tanlangan davrdagi imtihonlar (totalExams uchun Exam ID'lari to'plami)
        $examIds = Exam::where('subject_id', $subjectId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->when($classId, function ($q) use ($classId) {
                // Sinf filtri qo'llanilganda
                return $q->whereHas('user', function ($query) use ($classId) {
                    $query->where('classes_id', $classId);
                });
            })
            ->pluck('id');

        $totalExams = $examIds->count();

        // Unikal o'quvchilar soni
        $uniqueStudents = Exam::whereIn('id', $examIds)
            ->distinct('user_id')
            ->count('user_id');

        // O'rtacha muvaffaqiyat foizi
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
        // 3. SINFLAR BO'YICHA PERFORMANS (Donut Chart va Top List uchun)
        // ==========================================

        $classesQuery = Classes::where('status', 1);
        if ($classId) {
            $classesQuery->where('id', $classId);
        }
        $allClasses = $classesQuery->orderBy('name')->get();

        $classPerformance = [];
        foreach ($allClasses as $class) {

            $studentIds = Users::where('classes_id', $class->id)
                ->where('user_type', Users::TYPE_STUDENT)
                ->where('status', Users::STATUS_ACTIVE)
                ->pluck('id');

            if ($studentIds->isEmpty()) {
                continue;
            }

            $classExamIds = Exam::where('subject_id', $subjectId)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->whereIn('user_id', $studentIds)
                ->pluck('id');

            if ($classExamIds->isEmpty()) {
                continue;
            }

            $classTotal = ExamAnswer::whereIn('exam_id', $classExamIds)->count();

            $classCorrect = ExamAnswer::whereIn('exam_id', $classExamIds)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('option')
                        ->whereColumn('option.id', 'exam_answer.option_id')
                        ->where('option.is_correct', 1);
                })
                ->count();

            $percentage = $classTotal > 0 ? round(($classCorrect / $classTotal) * 100, 1) : 0;

            if ($classTotal > 0) {
                $classPerformance[] = [
                    'name' => $class->name,
                    'percentage' => $percentage,
                    'y' => (float) $percentage, // ApexCharts uchun y-o'qi qiymati
                    'total_answers' => $classTotal,
                    'correct_answers' => $classCorrect,
                ];
            }
        }

        // Top 10 Sinflarni tayyorlash
        $topClassesByPerformance = collect($classPerformance)
            ->sortByDesc('percentage')
            ->take(10)
            ->toArray();


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

        // ... (weekly activity logic)

        $weeklyActivity = [];
        $days = ['Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba', 'Yakshanba'];

        // MySQL/MariaDB uchun WEEKDAY() dan foydalanamiz (0=Dushanba)
        // Agar sizning bazangizda boshqacha bo'lsa, 'DAYOFWEEK(created_at)' ga moslab oling.
        $dayMap = [1 => 'Yakshanba', 2 => 'Dushanba', 3 => 'Seshanba', 4 => 'Chorshanba', 5 => 'Payshanba', 6 => 'Juma', 7 => 'Shanba']; // DAYOFWEEK - default

        foreach ($days as $index => $day) {
            $dbDayNumber = $index; // 0=Dushanba (WEEKDAY() ga mos)

            $count = Exam::where('subject_id', $subjectId)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->whereRaw('WEEKDAY(created_at) = ?', [$dbDayNumber]) // WEEKDAY: 0=Dushanba, 6=Yakshanba
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
                'questions', // Savollar sonini ham olamiz
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
