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
    public $className = "6 - Muhammad Yusuf";
    public $teacherName = '';
    public $quarter = 'Sentyabr'; // Chorak

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount($classId = null)
    {
        if ($classId) {
            $this->classFilter = $classId;
            $class = Classes::find($classId);
            if ($class) {
                $this->className = $class->name;
            }
        }
    }

    public function render()
    {
        // O'quvchilar va ularning natijalarini olish
        $students = DB::table('users')
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'classes.name as class_name',
                'classes.id as class_id',
            ])
            ->leftJoin('classes', 'classes.id', '=', 'users.classes_id')
            ->where('users.user_type', Users::TYPE_STUDENT)
            ->when($this->classFilter, function ($query) {
                $query->where('users.classes_id', $this->classFilter);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('users.first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('users.last_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('users.first_name')
            ->paginate(20);

        // Har bir o'quvchi uchun natijalarni hisoblash
        foreach ($students as $index => $student) {
            $student->number = ($students->currentPage() - 1) * $students->perPage() + $index + 1;

            // Fanlar bo'yicha natijalar
            $subjects = DB::table('exam')
                ->select([
                    'subjects.id as subject_id',
                    'subjects.name as subject_name',
                    DB::raw('COUNT(DISTINCT exam.quiz_id) as test_count'),
                    DB::raw('SUM(CASE WHEN ea.option_id = correct_opt.id THEN 1 ELSE 0 END) as correct_answers'),
                    DB::raw('COUNT(ea.id) as total_questions')
                ])
                ->join('quiz', 'quiz.id', '=', 'exam.quiz_id')
                ->join('subjects', 'subjects.id', '=', 'quiz.subject_id')
                ->join('exam_answer as ea', 'ea.exam_id', '=', 'exam.id')
                ->join('question', 'question.id', '=', 'ea.question_id')
                ->leftJoin('option as correct_opt', function($join) {
                    $join->on('correct_opt.question_id', '=', 'question.id')
                         ->where('correct_opt.is_correct', 1);
                })
                ->where('exam.user_id', $student->id)
                ->when($this->subjectFilter, function ($query) {
                    $query->where('subjects.id', $this->subjectFilter);
                })
                ->groupBy('subjects.id', 'subjects.name')
                ->get();

            // Xulqiqo'l va kirpi vazifalar (default qiymatlar)
            $student->conduct_grade = 'A' . rand(1, 2);
            $student->conduct_score = rand(80, 100);
            $student->homework_grade = 'A1';
            $student->homework_score = rand(50, 75);

            // Kitobxonlik (default)
            $student->reading_score = rand(40, 70);

            // Umumiy ball va o'rta
            $totalScore = 0;
            $subjectCount = 0;

            $student->subjects = $subjects->map(function($subject) use (&$totalScore, &$subjectCount) {
                $score = $subject->total_questions > 0 
                    ? round(($subject->correct_answers / $subject->total_questions) * 100) 
                    : 0;
                
                $subject->score = $score;
                $subject->grade = $this->getGrade($score);
                
                $totalScore += $score;
                $subjectCount++;
                
                return $subject;
            });

            // Xulqiqo'l va kirpi vazifalarni qo'shish
            $totalScore += $student->conduct_score + $student->homework_score + $student->reading_score;
            $subjectCount += 3;

            $student->total_score = $totalScore;
            $student->average_score = $subjectCount > 0 ? round($totalScore / $subjectCount) : 0;
            $student->overall_grade = $this->getGrade($student->average_score);
            $student->rank = ''; // Keyinchalik reyting qo'shiladi
        }

        // Reytingni hisoblash
        $studentsCollection = $students->getCollection()->sortByDesc('total_score')->values();
        foreach ($studentsCollection as $index => $student) {
            $student->rank = $index + 1;
        }

        $students->setCollection($studentsCollection);

        return view('livewire.koordinator.exam.student-monitoring', [
            'students' => $students,
            'classes' => Classes::where('status', 1)->get(),
            'subjects' => Subjects::where('status', 1)->get(),
        ]);
    }

    private function getGrade($score)
    {
        if ($score >= 86) return 'A1';
        if ($score >= 71) return 'A2';
        if ($score >= 56) return 'B1';
        if ($score >= 46) return 'B2';
        return 'C';
    }
}