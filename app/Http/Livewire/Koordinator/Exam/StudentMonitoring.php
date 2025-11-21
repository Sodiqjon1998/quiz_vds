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
            // ✅ Oddiy to'g'ridan-to'g'ri taqqoslash
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

        if ($students->isEmpty()) {
            \Log::warning('No students found for class: ' . $this->classFilter);
        }

        // Har bir o'quvchi uchun natijalarni hisoblash
        foreach ($students as $index => $student) {
            $student->number = ($students->currentPage() - 1) * $students->perPage() + $index + 1;

            \Log::info("Processing student: {$student->first_name} {$student->last_name} (ID: {$student->id})");

            // Quiz uchun ham oddiy taqqoslash
            $subjects = DB::table('exam')
                ->select([
                    'subjects.id as subject_id',
                    'subjects.name as subject_name',
                    DB::raw('COUNT(DISTINCT exam.id) as exam_count'),
                    DB::raw('SUM(CASE 
                    WHEN option.is_correct = 1 THEN 1 
                    ELSE 0 
                END) as correct_answers'),
                    DB::raw('COUNT(exam_answer.id) as total_questions')
                ])
                ->join('quiz', 'quiz.id', '=', 'exam.quiz_id')
                ->join('subjects', 'subjects.id', '=', 'quiz.subject_id')
                ->leftJoin('exam_answer', 'exam_answer.exam_id', '=', 'exam.id')
                ->leftJoin('option', 'option.id', '=', 'exam_answer.option_id')
                ->where('exam.user_id', $student->id)
                // ✅ Quiz uchun ham oddiy taqqoslash
                ->where('quiz.classes_id', $this->classFilter)
                ->when($this->subjectFilter, function ($query) {
                    $query->where('subjects.id', $this->subjectFilter);
                })
                ->groupBy('subjects.id', 'subjects.name')
                ->get();

            \Log::info("Student {$student->first_name} subjects count: " . $subjects->count());

            if ($subjects->isEmpty()) {
                \Log::warning("No exam data for student: {$student->first_name} {$student->last_name}");

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
                $student->conduct_grade = 'A' . rand(1, 2);
                $student->conduct_score = rand(80, 100);
                $student->homework_grade = 'A1';
                $student->homework_score = rand(50, 75);
                $student->reading_score = rand(40, 70);

                $totalScore = 0;
                $subjectCount = 0;

                $student->subjects = $subjects->map(function ($subject) use (&$totalScore, &$subjectCount) {
                    $score = $subject->total_questions > 0
                        ? round(($subject->correct_answers / $subject->total_questions) * 100)
                        : 0;

                    $subject->score = $score;
                    $subject->grade = $this->getGrade($score);

                    $totalScore += $score;
                    $subjectCount++;

                    return $subject;
                });

                $totalScore += $student->conduct_score + $student->homework_score + $student->reading_score;
                $subjectCount += 3;

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
