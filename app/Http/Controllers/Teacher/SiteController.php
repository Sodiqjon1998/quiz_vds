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
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException; // Va buni ham

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teacher = Auth::user();
        $subjectId = $teacher->subject_id;

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Barcha faol sinflarni olish
        $allClasses = Classes::where('status', 1)->get();

        // ===================================
        // === 1. KPI Metrikalarini Hisoblash ===
        // ===================================

        // A. Tizimdagi barcha faol o'quvchilar soni
        $totalStudentsInSystem = Users::where('user_type', Users::TYPE_STUDENT)
            ->where('status', 1)
            ->count();

        // B. O'qituvchi tomonidan yaratilgan testlar soni
        $totalQuizzesCreated = Quiz::where('created_by', $teacher->id)
            ->where('subject_id', $subjectId)
            ->count();

        // C. Joriy oyda o'qituvchining fanidan olingan jami imtihonlar soni
        $totalExamsTakenThisMonth = Exam::where('subject_id', $subjectId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        // D. O'qituvchining fani bo'yicha umumiy o'rtacha muvaffaqiyat foizi (All-time)
        $globalCorrectAnswers = 0;
        $globalAttemptedQuestions = 0; // ✅ Xato to'g'irlandi: e'lon qilindi

        $allExamsInSubject = Exam::where('subject_id', $subjectId)->get();

        foreach ($allExamsInSubject as $exam) {
            $examAnswers = ExamAnswer::where('exam_id', $exam->id)->get();

            foreach ($examAnswers as $answer) {
                $globalAttemptedQuestions++; // ✅ Xato to'g'irlandi: to'g'ri nom ishlatildi

                $correctOption = Option::where('question_id', $answer->question_id)
                    ->where('is_correct', 1)
                    ->first();

                if ($correctOption && $answer->option_id == $correctOption->id) {
                    $globalCorrectAnswers++;
                }
            }
        }

        $averageSuccessRate = ($globalAttemptedQuestions > 0) ? round(($globalCorrectAnswers / $globalAttemptedQuestions) * 100, 2) : 0; // ✅ Xato to'g'irlandi

        // =========================================================================
        // === 2. Chart va List Ma'lumotlarini Yig'ish (Yangi talab) ===
        // =========================================================================

        // A. Oylik Imtihonlar ma'lumotlari (Oxirgi 6 oy)
        $monthlyExamsData = [];
        $monthsLabels = [];

        $date = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $month = $date->copy()->subMonths($i);

            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $examCount = Exam::where('subject_id', $subjectId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $monthlyExamsData[] = $examCount;
            // 'M' formatida qisqa oy nomlari (Yan, Fev, Mart...)
            $monthsLabels[] = $month->translatedFormat('M');
        }

        // B. Sinflar bo'yicha to'g'ri javob foizi (Joriy oy uchun) (Donut Chart va Top List uchun asos)
        $classQuizPerformance = [];

        foreach ($allClasses as $class) {
            $totalCorrectAnswers = 0;
            $totalAttemptedQuestions = 0;

            $usersInClass = Users::where('classes_id', $class->id)->get();

            foreach ($usersInClass as $user) {
                $exams = Exam::where('user_id', $user->id)
                    ->where('subject_id', '=', $subjectId)
                    ->whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $currentMonth)
                    ->get();

                foreach ($exams as $exam) {
                    $examAnswers = ExamAnswer::where('exam_id', $exam->id)->get();

                    foreach ($examAnswers as $answer) {
                        $totalAttemptedQuestions++;

                        $correctOption = Option::where('question_id', $answer->question_id)
                            ->where('is_correct', 1)
                            ->first();

                        if ($correctOption && $answer->option_id == $correctOption->id) {
                            $totalCorrectAnswers++;
                        }
                    }
                }
            }

            $percentage = ($totalAttemptedQuestions > 0) ? round(($totalCorrectAnswers / $totalAttemptedQuestions) * 100, 2) : 0;

            $classQuizPerformance[] = [
                'name' => $class->name,
                'y' => (float) $percentage,
                'percentage' => (float) $percentage // Ro'yxat uchun foizni alohida saqlash
            ];
        }

        // C. Top 5 sinflar (Top List uchun: foiz bo'yicha saralash)
        $topClassesByPerformance = collect($classQuizPerformance)
            ->sortByDesc('percentage')
            ->take(5)
            ->toArray();

        // D. Top 5 faol o'quvchilar (Eng ko'p test topshirganlar)
        $topActiveStudents = Users::selectRaw('users.id, users.first_name, users.last_name, users.classes_id, count(exam.id) as exam_count')
            ->join('exam', 'users.id', '=', 'exam.user_id')
            ->where('exam.subject_id', $subjectId)
            ->where('users.user_type', Users::TYPE_STUDENT)
            ->where('users.status', 1)
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.classes_id')
            ->orderByDesc('exam_count')
            ->take(5)
            ->with('classRelation')
            ->get()
            ->map(function ($student) {
                return [
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'class_name' => $student->classRelation->name ?? 'N/A',
                    'exam_count' => $student->exam_count,
                ];
            })->toArray();


        // =========================================================================
        // === 3. Viewga ma'lumotlarni uzatish ===
        // =========================================================================

        return view('teacher.site.index', [
            // KPI Data
            'totalStudentsInSystem' => $totalStudentsInSystem,
            'totalQuizzesCreated' => $totalQuizzesCreated,
            'totalExamsTakenThisMonth' => $totalExamsTakenThisMonth,
            'averageSuccessRate' => $averageSuccessRate,

            // Chart Data for ApexCharts
            'monthlyExamsData' => $monthlyExamsData,
            'monthsLabels' => $monthsLabels,
            'classQuizPerformance' => $classQuizPerformance, // Donut Chart uchun

            // List Data
            'topClassesByPerformance' => $topClassesByPerformance, // Top Sinflar Listi uchun
            'topActiveStudents' => $topActiveStudents, // Top Faol O'quvchilar Listi uchun
        ]);
    }



    private function getDiskSpace()
    {
        $process = Process::fromShellCommandline('df -h'); // df -h buyrug'ini bajaramiz
        $process->run();

        // Xatolik yuz bersa
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput(); // Buyruq natijasini olamiz

        // Natijani tahlil qilish
        $lines = explode("\n", $output);
        $diskInfo = [];

        // Odatda, sizning loyihangiz joylashgan "/" yoki "/var/www" bo'limini qidirasiz
        foreach ($lines as $line) {
            // Birinchi qator (header) yoki bo'sh qatorlarni o'tkazib yuboramiz
            if (str_starts_with($line, 'Filesystem') || empty(trim($line))) {
                continue;
            }

            // Bo'sh joylar bo'yicha ajratamiz va toza ma'lumotlarni olamiz
            $parts = preg_split('/\s+/', $line);

            // Sizga kerakli bo'limni aniqlang (masalan, root "/" yoki /var/www)
            // Bu yerda Mounted on ustuni asosida qidiramiz
            if (isset($parts[5]) && ($parts[5] === '/' || $parts[5] === '/var/www')) { // O'zgartirishingiz mumkin
                $diskInfo = [
                    'filesystem' => $parts[0],
                    'size'       => $parts[1],
                    'used'       => $parts[2],
                    'available'  => $parts[3],
                    'usage_percent' => $parts[4],
                    'mounted_on' => $parts[5],
                ];
                break; // Kerakli bo'limni topgach to'xtaymiz
            }
        }

        // Agar hech qanday ma'lumot topilmasa yoki xatolik bo'lsa
        if (empty($diskInfo)) {
            return [
                'available' => 'N/A',
                'usage_percent' => 'N/A',
                'error' => 'Disk maʼlumotlari topilmadi yoki xatolik yuz berdi.'
            ];
        }

        return $diskInfo;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
