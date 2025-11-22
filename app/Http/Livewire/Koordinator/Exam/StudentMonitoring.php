<?php

namespace App\Http\Livewire\Koordinator\Exam;

use App\Models\Users;
use App\Models\Classes;
use App\Models\Subjects;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

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
                'showEmptyMessage' => true,
            ]);
        }

        \Log::info('CLASS FILTER: ' . $this->classFilter);

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

        \Log::info('STUDENTS COUNT: ' . $students->count());

        $dateRange = $this->getQuarterDateRange($this->quarter);

        foreach ($students as $index => $student) {
            $student->number = ($students->currentPage() - 1) * $students->perPage() + $index + 1;

            // Fanlar bo'yicha testlar
            $subjects = DB::table('exam')
                ->select([
                    'subjects.id as subject_id',
                    'subjects.name as subject_name',
                    DB::raw('COUNT(DISTINCT exam.id) as exam_count'),
                    DB::raw('SUM(CASE WHEN option.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                    DB::raw('COUNT(exam_answer.id) as total_questions')
                ])
                ->join('quiz', 'quiz.id', '=', 'exam.quiz_id')
                ->join('subjects', 'subjects.id', '=', 'quiz.subject_id')
                ->leftJoin('exam_answer', 'exam_answer.exam_id', '=', 'exam.id')
                ->leftJoin('option', 'option.id', '=', 'exam_answer.option_id')
                ->where('exam.user_id', $student->id)
                ->where('quiz.classes_id', $this->classFilter)
                ->whereBetween('exam.created_at', [$dateRange['start'], $dateRange['end']])
                ->when($this->subjectFilter, function ($query) {
                    $query->where('subjects.id', $this->subjectFilter);
                })
                ->groupBy('subjects.id', 'subjects.name')
                ->get();

            // Xulqatvor (conduct) - agar jadvalingiz bo'lsa
            $conductData = $this->getConductData($student->id, $dateRange);
            
            // Uy vazifalari (homework) - agar jadvalingiz bo'lsa
            $homeworkData = $this->getHomeworkData($student->id, $dateRange);
            
            // Kitobxonlik - agar jadvalingiz bo'lsa
            $readingData = $this->getReadingData($student->id, $dateRange);

            if ($subjects->isEmpty() && !$conductData && !$homeworkData && !$readingData) {
                $student->subjects = collect([]);
                $student->conduct_grade = '-';
                $student->conduct_score = 0;
                $student->homework_grade = '-';
                $student->homework_score = 0;
                $student->reading_score = 0;
                $student->total_score = 0;
                $student->average_score = 0;
                $student->overall_grade = '-';
            } else {
                $totalScore = 0;
                $subjectCount = 0;

                // Fanlar
                $student->subjects = $subjects->map(function($subject) use (&$totalScore, &$subjectCount) {
                    $score = $subject->total_questions > 0 
                        ? round(($subject->correct_answers / $subject->total_questions) * 100) 
                        : 0;
                    
                    $subject->score = $score;
                    $subject->grade = $this->getGrade($score);
                    
                    if ($score > 0) {
                        $totalScore += $score;
                        $subjectCount++;
                    }
                    
                    return $subject;
                });

                // Xulqatvor
                if ($conductData) {
                    $student->conduct_grade = $conductData['grade'];
                    $student->conduct_score = $conductData['score'];
                    $totalScore += $conductData['score'];
                    $subjectCount++;
                } else {
                    $student->conduct_grade = '-';
                    $student->conduct_score = 0;
                }

                // Uy vazifalari
                if ($homeworkData) {
                    $student->homework_grade = $homeworkData['grade'];
                    $student->homework_score = $homeworkData['score'];
                    $totalScore += $homeworkData['score'];
                    $subjectCount++;
                } else {
                    $student->homework_grade = '-';
                    $student->homework_score = 0;
                }

                // Kitobxonlik
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
        }

        // Reytingni hisoblash
        $studentsCollection = $students->getCollection()->sortByDesc('total_score')->values();
        foreach ($studentsCollection as $index => $student) {
            $student->rank = $index + 1;
        }

        $students->setCollection($studentsCollection->sortBy('first_name')->values());

        return view('livewire.koordinator.exam.student-monitoring', [
            'students' => $students,
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