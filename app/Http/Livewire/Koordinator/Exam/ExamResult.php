<?php

namespace App\Http\Livewire\Koordinator\Exam;

use App\Models\Users;
use App\Models\Classes;
use App\Models\Subjects;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ExamResult extends Component
{
    use WithPagination;

    public $search = '';
    public $classFilter = '';
    public $subjectFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $studentId = null;

    // Batafsil natijalar uchun
    public $showDetailModal = false;
    public $selectedExam = null;
    public $examDetails = [];

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function viewDetails($examId)
    {
        $this->selectedExam = DB::table('exam')
            ->select([
                'exam.id',
                'exam.quiz_id',
                'exam.user_id',
                'exam.created_at',
                'quiz.name as quiz_name',
                'subjects.name as subject_name',
                'users.first_name',
                'users.last_name',
                'classes.name as class_name',
            ])
            ->join('quiz', 'quiz.id', '=', 'exam.quiz_id')
            ->join('subjects', 'subjects.id', '=', 'quiz.subject_id')
            ->join('users', 'users.id', '=', 'exam.user_id')
            ->join('classes', 'classes.id', '=', 'users.classes_id')
            ->where('exam.id', $examId)
            ->first();

        if (!$this->selectedExam) {
            session()->flash('error', 'Exam topilmadi');
            return;
        }

        // Savollar va javoblarni olish
        $this->examDetails = DB::table('exam_answer')
            ->select([
                'exam_answer.id',
                'exam_answer.question_id',
                'exam_answer.option_id',
                'question.name as question_text',
                'question.image as question_image',
                'selected_option.name as selected_answer',
                'selected_option.image as selected_image',
                'correct_option.id as correct_option_id',
                'correct_option.name as correct_answer',
                'correct_option.image as correct_image',
            ])
            ->join('question', 'question.id', '=', 'exam_answer.question_id')
            ->join('option as selected_option', 'selected_option.id', '=', 'exam_answer.option_id')
            ->join('option as correct_option', function ($join) {
                $join->on('correct_option.question_id', '=', 'question.id')
                    ->where('correct_option.is_correct', 1);
            })
            ->where('exam_answer.exam_id', $examId)
            ->get()
            ->map(function ($item, $index) {
                $item->is_correct = $item->option_id == $item->correct_option_id;
                $item->number = $index + 1;
                return $item;
            });

        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedExam = null;
        $this->examDetails = [];
    }

    public function render()
    {
        $students = DB::table('exam')
            ->select([
                'users.id as student_id',
                'users.first_name',
                'users.last_name',
                'classes.name as class_name',
                'classes.id as class_id',
                DB::raw('COUNT(DISTINCT exam.id) as total_exams'),
                DB::raw('COUNT(DISTINCT exam.quiz_id) as total_quizzes'),
            ])
            ->leftJoin('users', 'users.id', '=', 'exam.user_id')
            ->leftJoin('classes', 'classes.id', '=', 'users.classes_id')
            ->where('users.user_type', Users::TYPE_STUDENT)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('users.first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('users.last_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->classFilter, function ($query) {
                $query->where('users.classes_id', $this->classFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->where('exam.created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->where('exam.created_at', '<=', $this->dateTo . ' 23:59:59');
            })
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'classes.name', 'classes.id')
            ->orderBy('users.first_name')
            ->paginate(15);

        // Har bir student uchun exam ma'lumotlarini olish
        foreach ($students as $student) {
            $exams = DB::table('exam')
                ->select([
                    'exam.id',
                    'exam.quiz_id',
                    'exam.created_at',
                    'quiz.name as quiz_name',
                    'subjects.name as subject_name',
                    'subjects.id as subject_id',
                    // ✅ Optimallashtirish: Jami savollarni bitta so'rovda olish
                    DB::raw('(SELECT COUNT(*) FROM exam_answer WHERE exam_answer.exam_id = exam.id) as total_questions'),
                    // ✅ Optimallashtirish: To\'g\'ri javoblarni bitta so'rovda olish
                    DB::raw('(SELECT COUNT(*) FROM exam_answer 
                      JOIN `option` ON `option`.id = exam_answer.option_id 
                      WHERE exam_answer.exam_id = exam.id AND `option`.is_correct = 1) as correct_answers')
                ])
                ->join('quiz', 'quiz.id', '=', 'exam.quiz_id')
                ->join('subjects', 'subjects.id', '=', 'quiz.subject_id')
                ->where('exam.user_id', $student->student_id)
                // ... filtrlarni qo'shish (subjectFilter, dates) ...
                ->when($this->subjectFilter, function ($query) {
                    $query->where('quiz.subject_id', $this->subjectFilter);
                })
                ->when($this->dateFrom, function ($query) {
                    $query->where('exam.created_at', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function ($query) {
                    $query->where('exam.created_at', '<=', $this->dateTo . ' 23:59:59');
                })
                ->orderBy('exam.created_at', 'desc')
                ->get();

            // Endi PHP tomonda hisoblash osonlashadi (Bazaga so'rov yubormasdan)
            foreach ($exams as $exam) {
                // Bazadan tayyor kelgan sonlarni ishlatamiz
                $exam->percentage = $exam->total_questions > 0
                    ? round(($exam->correct_answers / $exam->total_questions) * 100, 2)
                    : 0;
                $exam->passed = $exam->percentage >= 70;
            }

            $student->exams = $exams;
        }

        $totalExams = DB::table('exam')->count();
        $example = DB::table('exam')->limit(5)->get();
        \Log::info('EXAM_COUNT: ' . $totalExams);
        \Log::info('EXAM_EXAMPLE: ' . json_encode($example));

        return view('livewire.koordinator.exam.exam-results', [
            'students' => $students,
            'classes' => Classes::where('status', 1)->get(),
            'subjects' => Subjects::where('status', 1)->get(),
        ]);
    }
}
