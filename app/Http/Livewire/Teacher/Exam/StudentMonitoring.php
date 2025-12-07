<?php

namespace App\Http\Livewire\Teacher\Exam;

use App\Models\Users;
use App\Models\Classes;
use App\Models\Subjects;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Exports\StudentMonitoringExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class StudentMonitoring extends Component
{
    use WithPagination;

    public $classFilter = '';
    public $subjectFilter = '';
    public $search = '';
    public $schoolName = "ANDIJAN RISING SCHOOL";
    public $className = '';
    public $quarter = 'Noyabr';

    protected $paginationTheme = 'bootstrap';

    public function exportToExcel()
    {
        if (!$this->classFilter) {
            session()->flash('error', 'Iltimos, avval sinfni tanlang!');
            return;
        }

        // Barcha o'quvchilarni olish (paginationsiz)
        $studentsQuery = Users::select([
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.classes_id'
        ])
            ->where('users.user_type', Users::TYPE_STUDENT)
            ->where('users.status', 1)
            ->where('classes_id', $this->classFilter)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('users.first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('users.last_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('users.first_name')
            ->get();

        $dateRange = $this->getQuarterDateRange($this->quarter);

        $availableSubjects = DB::table('exam')
            ->select([
                'subjects.id',
                'subjects.name'
            ])
            ->join('quiz', 'quiz.id', '=', 'exam.quiz_id')
            ->join('subjects', 'subjects.id', '=', 'quiz.subject_id')
            ->join('users', 'users.id', '=', 'exam.user_id')
            ->where('users.classes_id', $this->classFilter)
            ->whereBetween('exam.created_at', [$dateRange['start'], $dateRange['end']])
            ->when($this->subjectFilter, function ($query) {
                $query->where('subjects.id', $this->subjectFilter);
            })
            ->groupBy('subjects.id', 'subjects.name')
            ->orderBy('subjects.name')
            ->get();

        foreach ($studentsQuery as $student) {
            $subjectsData = [];

            foreach ($availableSubjects as $availableSubject) {
                $examResults = DB::table('exam')
                    ->select([
                        'exam.id as exam_id',
                        DB::raw('COUNT(exam_answer.id) as total_questions'),
                        DB::raw('SUM(CASE WHEN option.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
                    ])
                    ->join('quiz', 'quiz.id', '=', 'exam.quiz_id')
                    ->leftJoin('exam_answer', 'exam_answer.exam_id', '=', 'exam.id')
                    ->leftJoin('option', 'option.id', '=', 'exam_answer.option_id')
                    ->where('exam.user_id', $student->id)
                    ->where('quiz.subject_id', $availableSubject->id)
                    ->whereBetween('exam.created_at', [$dateRange['start'], $dateRange['end']])
                    ->groupBy('exam.id')
                    ->get();

                $maxScore = 0;
                $examCount = $examResults->count();

                foreach ($examResults as $result) {
                    if ($result->total_questions > 0) {
                        $score = round(($result->correct_answers / $result->total_questions) * 100);
                        if ($score > $maxScore) {
                            $maxScore = $score;
                        }
                    }
                }

                $subjectsData[$availableSubject->id] = [
                    'subject_id' => $availableSubject->id,
                    'subject_name' => $availableSubject->name,
                    'score' => $maxScore,
                    'grade' => $this->getGrade($maxScore),
                    'exam_count' => $examCount
                ];
            }

            $student->subjectsData = collect($subjectsData);

            $conductData = $this->getConductData($student->id, $dateRange);
            $homeworkData = $this->getHomeworkData($student->id, $dateRange);
            $readingData = $this->getReadingData($student->id, $dateRange);

            $totalScore = 0;
            $subjectCount = 0;

            foreach ($subjectsData as $data) {
                if ($data['score'] > 0) {
                    $totalScore += $data['score'];
                    $subjectCount++;
                }
            }

            if ($conductData) {
                $student->conduct_grade = $conductData['grade'];
                $student->conduct_score = $conductData['score'];
                $totalScore += $conductData['score'];
                $subjectCount++;
            } else {
                $student->conduct_grade = '-';
                $student->conduct_score = 0;
            }

            if ($readingData) {
                $student->reading_score = $readingData['score'];
                $totalScore += $readingData['score'];
                $subjectCount++;
            } else {
                $student->reading_score = 0;
            }

            $student->total_score = $totalScore;
            $student->average_score = $subjectCount > 0 ? round($totalScore / $subjectCount) : 0;
        }

        // Reytingni hisoblash
        $studentsQuery = $studentsQuery->sortByDesc('total_score')->values();
        foreach ($studentsQuery as $index => $student) {
            $student->rank = $index + 1;
        }

        $fileName = $this->className . ' - ' . $this->quarter . ' - ' . date('Y-m-d') . '.xlsx';

        return Excel::download(
            new StudentMonitoringExport($studentsQuery, $availableSubjects, $this->schoolName, $this->className, $this->quarter),
            $fileName
        );
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingClassFilter()
    {
        $this->resetPage();
        $this->updateClassName();
    }

    public function mount($classId = null)
    {
        if ($classId) {
            $this->classFilter = $classId;
        }

        // --- YANGI QO'SHILGAN QISM ---
        // Joriy oyni aniqlab, $quarter o'zgaruvchisiga yuklaymiz
        $currentMonth = Carbon::now()->month;

        $months = [
            1 => 'Yanvar',
            2 => 'Fevral',
            3 => 'Mart',
            4 => 'Aprel',
            5 => 'May',
            9 => 'Sentyabr',
            10 => 'Oktyabr',
            11 => 'Noyabr',
            12 => 'Dekabr',
        ];

        // Agar hozirgi oy ro'yxatda bo'lsa (o'quv yili ichida), o'shani tanlaymiz.
        // Yoz oylari (6, 7, 8) bo'lsa, 'Sentyabr' ga tushadi yoki o'zingiz xohlagan oyni qo'yishingiz mumkin.
        $this->quarter = $months[$currentMonth] ?? 'Sentyabr';
        // ------------------------------

        $this->updateClassName();
    }

    private function updateClassName()
    {
        if ($this->classFilter) {
            $class = Classes::find($this->classFilter);
            if ($class) {
                $this->className = $class->name;
            }
        } else {
            $this->className = '';
        }
    }

    public function render()
    {
        // 1. Agar sinf tanlanmagan bo'lsa, bo'sh sahifa qaytarish
        if (!$this->classFilter) {
            return view('livewire.teacher.exam.student-monitoring', [
                'students' => collect([]),
                'classes' => Classes::where('status', 1)->orderBy('name')->get(),
                'subjects' => Subjects::where('status', 1)->orderBy('name')->get(),
                'availableSubjects' => collect([]),
                'showEmptyMessage' => true,
            ]);
        }

        // 2. Kesh kalitini yaratish (Sinf, fan, qidiruv, chorak va sahifa raqami bo'yicha unikal)
        // Sahifa raqamini olish (kesh to'g'ri ishlashi uchun juda muhim)
        $page = request()->input('page', 1);

        $cacheKey = 'monitoring_v1_' .
            $this->classFilter . '_' .
            $this->subjectFilter . '_' .
            $this->quarter . '_' .
            $this->search . '_page_' . $page;

        // Sana oralig'ini oldindan olamiz (kesh funksiyasi ichida ishlatish uchun)
        $dateRange = $this->getQuarterDateRange($this->quarter);

        // 3. Cache::remember - Ma'lumotni keshdan olish yoki hisoblash
        // 600 soniya (10 daqiqa) davomida saqlanadi
        $cachedData = Cache::remember($cacheKey, 600, function () use ($dateRange) {

            // A. Studentlarni bazadan olish
            $studentsQuery = Users::select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.classes_id'
            ])
                ->where('users.user_type', Users::TYPE_STUDENT)
                ->where('users.status', 1)
                ->where('classes_id', $this->classFilter)
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('users.first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('users.last_name', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('users.first_name');

            $students = $studentsQuery->paginate(20);

            // B. Mavjud fanlarni olish
            $availableSubjects = DB::table('exam')
                ->select(['subjects.id', 'subjects.name'])
                ->join('quiz', 'quiz.id', '=', 'exam.quiz_id')
                ->join('subjects', 'subjects.id', '=', 'quiz.subject_id')
                ->join('users', 'users.id', '=', 'exam.user_id')
                ->where('users.classes_id', $this->classFilter)
                ->whereBetween('exam.created_at', [$dateRange['start'], $dateRange['end']])
                ->when($this->subjectFilter, function ($query) {
                    $query->where('subjects.id', $this->subjectFilter);
                })
                ->groupBy('subjects.id', 'subjects.name')
                ->orderBy('subjects.name')
                ->get();

            // C. Har bir student uchun hisob-kitoblar (Og'ir jarayon)
            foreach ($students as $index => $student) {
                $student->number = ($students->currentPage() - 1) * $students->perPage() + $index + 1;
                $subjectsData = [];

                foreach ($availableSubjects as $availableSubject) {
                    $examResults = DB::table('exam')
                        ->select([
                            'exam.id as exam_id',
                            DB::raw('COUNT(exam_answer.id) as total_questions'),
                            DB::raw('SUM(CASE WHEN option.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
                        ])
                        ->join('quiz', 'quiz.id', '=', 'exam.quiz_id')
                        ->leftJoin('exam_answer', 'exam_answer.exam_id', '=', 'exam.id')
                        ->leftJoin('option', 'option.id', '=', 'exam_answer.option_id')
                        ->where('exam.user_id', $student->id)
                        ->where('quiz.subject_id', $availableSubject->id)
                        ->whereBetween('exam.created_at', [$dateRange['start'], $dateRange['end']])
                        ->groupBy('exam.id')
                        ->get();

                    $maxScore = 0;
                    $examCount = $examResults->count();

                    foreach ($examResults as $result) {
                        if ($result->total_questions > 0) {
                            $score = round(($result->correct_answers / $result->total_questions) * 100);
                            if ($score > $maxScore) $maxScore = $score;
                        }
                    }

                    $subjectsData[$availableSubject->id] = [
                        'subject_id' => $availableSubject->id,
                        'subject_name' => $availableSubject->name,
                        'score' => $maxScore,
                        'grade' => $this->getGrade($maxScore),
                        'exam_count' => $examCount
                    ];
                }

                $student->subjectsData = collect($subjectsData);

                // Qo'shimcha ma'lumotlar (Xulq, Uy vazifa, Kitob)
                $conductData = $this->getConductData($student->id, $dateRange);
                $homeworkData = $this->getHomeworkData($student->id, $dateRange);
                $readingData = $this->getReadingData($student->id, $dateRange);

                // Umumiy ballni hisoblash
                $totalScore = 0;
                $subjectCount = 0;
                foreach ($subjectsData as $data) {
                    if ($data['score'] > 0) {
                        $totalScore += $data['score'];
                        $subjectCount++;
                    }
                }

                if ($conductData) {
                    $student->conduct_grade = $conductData['grade'];
                    $student->conduct_score = $conductData['score'];
                    $totalScore += $conductData['score'];
                    $subjectCount++;
                } else {
                    $student->conduct_grade = '-';
                    $student->conduct_score = 0;
                }

                if ($homeworkData) {
                    $student->homework_grade = $homeworkData['grade'];
                    $student->homework_score = $homeworkData['score'];
                    $totalScore += $homeworkData['score'];
                    $subjectCount++;
                } else {
                    $student->homework_grade = '-';
                    $student->homework_score = 0;
                }

                if ($readingData) {
                    $student->reading_score = $readingData['score'];
                    $totalScore += $readingData['score'];
                    $subjectCount++;
                } else {
                    $student->reading_score = 0;
                }

                $student->total_score = $totalScore;
                $student->average_score = $subjectCount > 0 ? round($totalScore / $subjectCount) : 0;
                $student->overall_grade = $this->getGrade($student->average_score);
            }

            // D. Reyting bo'yicha saralash
            $studentsCollection = $students->getCollection()->sortByDesc('total_score')->values();
            foreach ($studentsCollection as $index => $student) {
                $student->rank = $index + 1;
            }
            $students->setCollection($studentsCollection->sortBy('first_name')->values());

            // Keshga saqlash uchun array qaytaramiz (chunki ikkita o'zgaruvchi kerak)
            return [
                'students' => $students,
                'availableSubjects' => $availableSubjects
            ];
        });

        // 4. Keshdan olingan ma'lumotlarni Viewga uzatish
        return view('livewire.teacher.exam.student-monitoring', [
            'students' => $cachedData['students'],
            'availableSubjects' => $cachedData['availableSubjects'],
            // Quyidagilarni keshlamadik, chunki ular yengil so'rovlar
            'classes' => Classes::where('status', 1)->orderBy('name')->get(),
            'subjects' => Subjects::where('status', 1)->orderBy('name')->get(),
            'showEmptyMessage' => false,
        ]);
    }

    /**
     * Xulqatvor ma'lumotlarini olish
     * Agar jadvalingiz bo'lsa, shu yerda real queryni yozing
     */
    private function getConductData($studentId, $dateRange)
    {
        // Misol: Agar conduct jadvali bo'lsa
        // $conduct = DB::table('conduct')
        //     ->where('student_id', $studentId)
        //     ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
        //     ->first();

        // if ($conduct) {
        //     return [
        //         'score' => $conduct->score,
        //         'grade' => $this->getGrade($conduct->score)
        //     ];
        // }

        // Hozircha null qaytaramiz (ma'lumot yo'q)
        return null;
    }

    /**
     * Uy vazifalari ma'lumotlarini olish
     */
    private function getHomeworkData($studentId, $dateRange)
    {
        // Misol: Agar homework jadvali bo'lsa
        // $homework = DB::table('homework')
        //     ->where('student_id', $studentId)
        //     ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
        //     ->first();

        // if ($homework) {
        //     return [
        //         'score' => $homework->score,
        //         'grade' => $this->getGrade($homework->score)
        //     ];
        // }

        return null;
    }

    /**
     * Kitobxonlik ma'lumotlarini olish
     */
    private function getReadingData($studentId, $dateRange)
    {
        // Misol: Agar reading jadvali bo'lsa
        // $reading = DB::table('reading')
        //     ->where('student_id', $studentId)
        //     ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
        //     ->sum('pages_read'); // yoki boshqa metrika

        // if ($reading > 0) {
        //     return ['score' => min($reading, 100)]; // Max 100
        // }

        return null;
    }

    /**
     * Chorak bo'yicha sana diapazonini qaytaradi
     */
    private function getQuarterDateRange($quarter)
    {
        $year = Carbon::now()->year;

        $quarters = [
            // 2024-2025 o'quv yili
            'Sentyabr' => [
                'start' => Carbon::create($year, 9, 1)->startOfDay(),
                'end' => Carbon::create($year, 9, 30)->endOfDay(),
            ],
            'Oktyabr' => [
                'start' => Carbon::create($year, 10, 1)->startOfDay(),
                'end' => Carbon::create($year, 10, 31)->endOfDay(),
            ],
            'Noyabr' => [
                'start' => Carbon::create($year, 11, 1)->startOfDay(),
                'end' => Carbon::create($year, 11, 30)->endOfDay(),
            ],
            'Dekabr' => [
                'start' => Carbon::create($year, 12, 1)->startOfDay(),
                'end' => Carbon::create($year, 12, 31)->endOfDay(),
            ],
            'Yanvar' => [
                'start' => Carbon::create($year + 1, 1, 1)->startOfDay(),
                'end' => Carbon::create($year + 1, 1, 31)->endOfDay(),
            ],
            'Fevral' => [
                'start' => Carbon::create($year + 1, 2, 1)->startOfDay(),
                'end' => Carbon::create($year + 1, 2, 28)->endOfDay(),
            ],
            'Mart' => [
                'start' => Carbon::create($year + 1, 3, 1)->startOfDay(),
                'end' => Carbon::create($year + 1, 3, 31)->endOfDay(),
            ],
            'Aprel' => [
                'start' => Carbon::create($year + 1, 4, 1)->startOfDay(),
                'end' => Carbon::create($year + 1, 4, 30)->endOfDay(),
            ],
            'May' => [
                'start' => Carbon::create($year + 1, 5, 1)->startOfDay(),
                'end' => Carbon::create($year + 1, 5, 31)->endOfDay(),
            ],
            // Choraklar
            '1-chorak' => [
                'start' => Carbon::create($year, 9, 1)->startOfDay(),
                'end' => Carbon::create($year, 11, 30)->endOfDay(),
            ],
            '2-chorak' => [
                'start' => Carbon::create($year, 12, 1)->startOfDay(),
                'end' => Carbon::create($year + 1, 2, 28)->endOfDay(),
            ],
            '3-chorak' => [
                'start' => Carbon::create($year + 1, 3, 1)->startOfDay(),
                'end' => Carbon::create($year + 1, 5, 31)->endOfDay(),
            ],
            // Yillik
            'Yillik' => [
                'start' => Carbon::create($year, 9, 1)->startOfDay(),
                'end' => Carbon::create($year + 1, 5, 31)->endOfDay(),
            ],
        ];

        return $quarters[$quarter] ?? [
            'start' => Carbon::now()->startOfMonth(),
            'end' => Carbon::now()->endOfMonth(),
        ];
    }

    private function getGrade($score)
    {
        if ($score >= 86) return 'A1';
        if ($score >= 71) return 'A2';
        if ($score >= 56) return 'B1';
        if ($score >= 46) return 'B2';
        if ($score > 0) return 'C';
        return '-';
    }
}
