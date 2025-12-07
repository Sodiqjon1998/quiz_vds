<?php

namespace App\Http\Livewire\Koordinator\Exam;

use App\Models\Users;
use App\Models\Classes;
use App\Models\Subjects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; // <--- Kesh uchun qo'shildi
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Exports\StudentMonitoringExport;
use Maatwebsite\Excel\Facades\Excel;

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

        // Excel uchun ma'lumotlarni tayyorlash (Keshsiz, chunki real vaqtda kerak bo'lishi mumkin)
        // Yoki bu yerni ham optimallashtirish mumkin, lekin hozircha asosiy e'tibor renderga
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

        // --- YANGI QO'SHILGAN QISM (Joriy oyni aniqlash) ---
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
        // Agar joriy oy ro'yxatda bo'lsa, o'shani olamiz, bo'lmasa 'Sentyabr'
        $this->quarter = $months[$currentMonth] ?? 'Sentyabr';
        // ----------------------------------------------------

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
        if (!$this->classFilter) {
            return view('livewire.koordinator.exam.student-monitoring', [
                'students' => collect([]),
                'classes' => Classes::where('status', 1)->orderBy('name')->get(),
                'subjects' => Subjects::where('status', 1)->orderBy('name')->get(),
                'availableSubjects' => collect([]),
                'showEmptyMessage' => true,
            ]);
        }

        // Kesh kalitini yaratamiz
        $page = request()->input('page', 1);
        $cacheKey = 'koordinator_monitoring_' .
            $this->classFilter . '_' .
            $this->subjectFilter . '_' .
            $this->quarter . '_' .
            $this->search . '_page_' . $page;

        $dateRange = $this->getQuarterDateRange($this->quarter);

        // Keshdan ma'lumot olish yoki hisoblash (10 daqiqa - 600 sekund)
        $cachedData = Cache::remember($cacheKey, 600, function () use ($dateRange) {

            // 1. O'quvchilarni olish
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

            // 2. Mavjud fanlarni olish
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

            // 3. Har bir o'quvchi uchun hisob-kitoblar
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

            // Reyting bo'yicha saralash
            $studentsCollection = $students->getCollection()->sortByDesc('total_score')->values();
            foreach ($studentsCollection as $index => $student) {
                $student->rank = $index + 1;
            }
            $students->setCollection($studentsCollection->sortBy('first_name')->values());

            return [
                'students' => $students,
                'availableSubjects' => $availableSubjects
            ];
        });

        // Sinflar va fanlar ro'yxatini ham keshga olish (1 soat)
        $classes = Cache::remember('all_classes_list', 3600, function () {
            return Classes::where('status', 1)->orderBy('name')->get();
        });

        $subjects = Cache::remember('all_subjects_list', 3600, function () {
            return Subjects::where('status', 1)->orderBy('name')->get();
        });

        return view('livewire.koordinator.exam.student-monitoring', [
            'students' => $cachedData['students'],
            'availableSubjects' => $cachedData['availableSubjects'],
            'classes' => $classes,
            'subjects' => $subjects,
            'showEmptyMessage' => false,
        ]);
    }

    private function getConductData($studentId, $dateRange)
    {
        return null;
    }
    private function getHomeworkData($studentId, $dateRange)
    {
        return null;
    }
    private function getReadingData($studentId, $dateRange)
    {
        return null;
    }

    private function getQuarterDateRange($quarter)
    {
        $year = Carbon::now()->year;
        $quarters = [
            'Sentyabr' => ['start' => Carbon::create($year, 9, 1)->startOfDay(), 'end' => Carbon::create($year, 9, 30)->endOfDay()],
            'Oktyabr' => ['start' => Carbon::create($year, 10, 1)->startOfDay(), 'end' => Carbon::create($year, 10, 31)->endOfDay()],
            'Noyabr' => ['start' => Carbon::create($year, 11, 1)->startOfDay(), 'end' => Carbon::create($year, 11, 30)->endOfDay()],
            'Dekabr' => ['start' => Carbon::create($year, 12, 1)->startOfDay(), 'end' => Carbon::create($year, 12, 31)->endOfDay()],
            'Yanvar' => ['start' => Carbon::create($year + 1, 1, 1)->startOfDay(), 'end' => Carbon::create($year + 1, 1, 31)->endOfDay()],
            'Fevral' => ['start' => Carbon::create($year + 1, 2, 1)->startOfDay(), 'end' => Carbon::create($year + 1, 2, 28)->endOfDay()],
            'Mart' => ['start' => Carbon::create($year + 1, 3, 1)->startOfDay(), 'end' => Carbon::create($year + 1, 3, 31)->endOfDay()],
            'Aprel' => ['start' => Carbon::create($year + 1, 4, 1)->startOfDay(), 'end' => Carbon::create($year + 1, 4, 30)->endOfDay()],
            'May' => ['start' => Carbon::create($year + 1, 5, 1)->startOfDay(), 'end' => Carbon::create($year + 1, 5, 31)->endOfDay()],
            '1-chorak' => ['start' => Carbon::create($year, 9, 1)->startOfDay(), 'end' => Carbon::create($year, 11, 30)->endOfDay()],
            '2-chorak' => ['start' => Carbon::create($year, 12, 1)->startOfDay(), 'end' => Carbon::create($year + 1, 2, 28)->endOfDay()],
            '3-chorak' => ['start' => Carbon::create($year + 1, 3, 1)->startOfDay(), 'end' => Carbon::create($year + 1, 5, 31)->endOfDay()],
            'Yillik' => ['start' => Carbon::create($year, 9, 1)->startOfDay(), 'end' => Carbon::create($year + 1, 5, 31)->endOfDay()],
        ];
        return $quarters[$quarter] ?? ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()->endOfMonth()];
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
