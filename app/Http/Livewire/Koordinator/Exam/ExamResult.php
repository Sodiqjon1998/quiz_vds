<?php

namespace App\Http\Livewire\Koordinator\Exam;

use App\Models\Classes;
use App\Models\Subjects;
use App\Models\Users; // Users modelini qo'shish kerak bo'lishi mumkin
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
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

    // O'zgarish: selectedExam boshlang'ich qiymati null yoki array bo'lishi kerak
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
        // 1. Exam ma'lumotlarini olish (LEFT JOIN)
        $examData = DB::table('exam')
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
            ->leftJoin('quiz', 'quiz.id', '=', 'exam.quiz_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'quiz.subject_id')
            ->leftJoin('users', 'users.id', '=', 'exam.user_id')
            ->leftJoin('classes', 'classes.id', '=', 'users.classes_id')
            ->where('exam.id', $examId)
            ->first();

        if (!$examData) {
            session()->flash('error', 'Exam topilmadi (ID: ' . $examId . ')');
            return;
        }

        // ✅ MUHIM: Obyektni Massivga (Array) aylantiramiz
        $this->selectedExam = (array) $examData;

        // 2. Savollar va javoblarni olish
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
            ->leftJoin('question', 'question.id', '=', 'exam_answer.question_id')
            ->leftJoin('option as selected_option', 'selected_option.id', '=', 'exam_answer.option_id')
            ->leftJoin('option as correct_option', function ($join) {
                $join->on('correct_option.question_id', '=', 'question.id')
                    ->where('correct_option.is_correct', 1);
            })
            ->where('exam_answer.exam_id', $examId)
            ->get()
            ->map(function ($item, $index) {
                // ✅ MUHIM: Har bir qatorni massivga aylantiramiz
                $item = (array) $item;

                $item['is_correct'] = $item['option_id'] && ($item['option_id'] == $item['correct_option_id']);
                $item['number'] = $index + 1;

                // O'chirilgan ma'lumotlar uchun
                if (!$item['question_text']) $item['question_text'] = '<em class="text-muted">Savol o\'chirilgan</em>';
                if (!$item['selected_answer']) $item['selected_answer'] = '<em class="text-muted">Belgilanmagan</em>';
                if (!$item['correct_answer']) $item['correct_answer'] = '-';

                return $item;
            })->toArray(); // ✅ Collection ni Array ga o'giramiz

        $this->showDetailModal = true;

        $this->dispatchBrowserEvent('renderMathJax');
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedExam = null;
        $this->examDetails = [];
    }

    public function render()
    {
        $page = request()->input('page', 1);
        $cacheKey = 'koordinator_exam_results_' .
            $this->classFilter . '_' .
            $this->subjectFilter . '_' .
            $this->dateFrom . '_' .
            $this->dateTo . '_' .
            $this->search . '_page_' . $page;

        $students = Cache::remember($cacheKey, 300, function () {

            $studentsQuery = DB::table('exam')
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

            foreach ($studentsQuery as $student) {
                $exams = DB::table('exam')
                    ->select([
                        'exam.id',
                        'exam.quiz_id',
                        'exam.created_at',
                        'quiz.name as quiz_name',
                        'subjects.name as subject_name',
                        'subjects.id as subject_id',
                        DB::raw('(SELECT COUNT(*) FROM exam_answer WHERE exam_answer.exam_id = exam.id) as total_questions'),
                        DB::raw('(SELECT COUNT(*) FROM exam_answer 
                          JOIN `option` ON `option`.id = exam_answer.option_id 
                          WHERE exam_answer.exam_id = exam.id AND `option`.is_correct = 1) as correct_answers')
                    ])
                    ->leftJoin('quiz', 'quiz.id', '=', 'exam.quiz_id')
                    ->leftJoin('subjects', 'subjects.id', '=', 'quiz.subject_id')
                    ->where('exam.user_id', $student->student_id)
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

                foreach ($exams as $exam) {
                    $exam->percentage = $exam->total_questions > 0
                        ? round(($exam->correct_answers / $exam->total_questions) * 100, 2)
                        : 0;
                    $exam->passed = $exam->percentage >= 70;
                }

                $student->exams = $exams;
            }

            return $studentsQuery;
        });

        $classes = Cache::remember('all_classes_active', 3600, fn() => Classes::where('status', 1)->get());
        $subjects = Cache::remember('all_subjects_active', 3600, fn() => Subjects::where('status', 1)->get());

        return view('livewire.koordinator.exam.exam-results', [
            'students' => $students,
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }
}
