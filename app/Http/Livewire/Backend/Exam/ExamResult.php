<?php

namespace App\Http\Livewire\Backend\Exam;

use App\Models\Classes;
use App\Models\Subjects;
use App\Models\Users;
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

    // Modal
    public $showDetailModal = false;
    public $selectedExam = null;
    public $examDetails = [];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
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
            ->leftJoin('classes', function ($join) {
                $join->on(DB::raw('CAST(users.classes_id AS UNSIGNED)'), '=', 'classes.id');
            })
            ->where('exam.id', $examId)
            ->first();

        if (!$examData) {
            session()->flash('error', 'Test topilmadi');
            return;
        }

        // Obyektni massivga o'girish
        $this->selectedExam = (array) $examData;

        // 2. Savollar va javoblarni olish
        $this->examDetails = DB::table('exam_answer')
            ->select([
                'exam_answer.id',
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
                $item = (array) $item;
                $item['is_correct'] = $item['option_id'] && ($item['option_id'] == $item['correct_option_id']);
                $item['number'] = $index + 1;

                // Bo'sh qiymatlarni to'ldirish
                if (!$item['question_text']) $item['question_text'] = '<em class="text-muted">Savol o\'chirilgan</em>';
                if (!$item['selected_answer']) $item['selected_answer'] = '<em class="text-muted">Belgilanmagan</em>';

                return $item;
            })->toArray();

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
        $cacheKey = 'admin_exam_results_' . $this->classFilter . '_' . $this->subjectFilter . '_' . $this->dateFrom . '_' . $this->dateTo . '_' . $this->search . '_page_' . $page;

        $results = Cache::remember($cacheKey, 300, function () {
            // Asosiy so'rov
            $query = DB::table('exam')
                ->select([
                    'exam.id',
                    'exam.created_at',
                    'users.first_name',
                    'users.last_name',
                    'classes.name as class_name',
                    'quiz.name as quiz_name',
                    'subjects.name as subject_name',
                    // Natijani hisoblash (Subquery)
                    DB::raw('(SELECT COUNT(*) FROM exam_answer WHERE exam_answer.exam_id = exam.id) as total_questions'),
                    DB::raw('(SELECT COUNT(*) FROM exam_answer 
                        JOIN `option` ON `option`.id = exam_answer.option_id 
                        WHERE exam_answer.exam_id = exam.id AND `option`.is_correct = 1) as correct_answers')
                ])
                ->leftJoin('users', 'users.id', '=', 'exam.user_id')
                ->leftJoin('classes', function ($join) {
                    $join->on(DB::raw('CAST(users.classes_id AS UNSIGNED)'), '=', 'classes.id');
                })
                ->leftJoin('quiz', 'quiz.id', '=', 'exam.quiz_id')
                ->leftJoin('subjects', 'subjects.id', '=', 'quiz.subject_id')
                ->where('users.user_type', Users::TYPE_STUDENT);

            // Filtrlar
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('users.first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('users.last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('quiz.name', 'like', '%' . $this->search . '%');
                });
            }
            if ($this->classFilter) $query->where('users.classes_id', $this->classFilter);
            if ($this->subjectFilter) $query->where('quiz.subject_id', $this->subjectFilter);
            if ($this->dateFrom) $query->where('exam.created_at', '>=', $this->dateFrom);
            if ($this->dateTo) $query->where('exam.created_at', '<=', $this->dateTo . ' 23:59:59');

            $items = $query->orderBy('exam.created_at', 'desc')->paginate(15);

            // Foizlarni hisoblash
            foreach ($items as $item) {
                $item->percentage = $item->total_questions > 0
                    ? round(($item->correct_answers / $item->total_questions) * 100)
                    : 0;
                $item->passed = $item->percentage >= 70;
            }

            return $items;
        });

        // Yordamchi ma'lumotlar (Kesh - 1 soat)
        $classes = Cache::remember('admin_classes_list', 3600, fn() => Classes::where('status', 1)->orderBy('name')->get());
        $subjects = Cache::remember('admin_subjects_list', 3600, fn() => Subjects::where('status', 1)->orderBy('name')->get());

        return view('livewire.backend.exam.exam-result', [
            'results' => $results,
            'classes' => $classes,
            'subjects' => $subjects
        ])
            ->extends('backend.layouts.main')
            ->section('content');
    }
}
