<?php

namespace App\Http\Livewire\Teacher\Exam;

use App\Models\Exam;
use App\Models\Teacher\Quiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; // Cache qo'shildi
use Livewire\Component;
use Livewire\WithPagination;

class ExamResultTest extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $classId = '';
    public $quizId = '';

    // Modal uchun
    public $showDetailModal = false;
    public $selectedExam = null;
    public $examStats = [];

    protected $listeners = ['showDetails'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingQuizId()
    {
        $this->resetPage();
    }

    public function updatingClassId()
    {
        $this->resetPage();
    }

    public function showDetails($examId)
    {
        // Bu funksiya modal ochilganda ishlaydi, shuning uchun bu yerda 
        // to'liq ma'lumotlarni (answers, options) yuklash normal holat.
        // Buni keshlashtirish shart emas, chunki foydalanuvchi har doim ham bossavermaydi.
        try {
            $this->selectedExam = Exam::with([
                'user',
                'quiz.subject',
                'answers.question',
                'answers.option'
            ])->findOrFail($examId);

            // Statistikani hisoblash
            $totalQuestions = $this->selectedExam->answers->count();
            $correctAnswers = $this->selectedExam->answers
                ->filter(fn($a) => $a->option && $a->option->is_correct)
                ->count();

            $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

            $this->examStats = [
                'total' => $totalQuestions,
                'correct' => $correctAnswers,
                'percentage' => $percentage
            ];

            $this->showDetailModal = true;

            // --- MUHIM QO'SHIMCHA ---
            // Modal ochilgandan keyin MathJaxni ishga tushirish
            $this->dispatchBrowserEvent('renderMathJax');
        } catch (\Exception $e) {
            Log::error('Error in showDetails: ' . $e->getMessage());
            $this->showDetailModal = false;
            session()->flash('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedExam = null;
        $this->examStats = [];
    }

    public function render()
    {
        $teacherId = Auth::id();
        $page = request()->input('page', 1);

        // 1. Sinflar va Quizlarni keshdan olish (kam o'zgaradigan ma'lumotlar)
        $classes = Cache::remember('all_classes_list', 3600, function () {
            return DB::table('classes')->orderBy('name')->get();
        });

        $quizzes = Cache::remember('teacher_quizzes_list_' . $teacherId, 600, function () use ($teacherId) {
            return Quiz::where('created_by', $teacherId)
                ->orderBy('created_at', 'desc')
                ->get();
        });

        // 2. Asosiy jadval uchun kesh kaliti
        $cacheKey = 'exam_test_list_' . $teacherId . '_' .
            $this->search . '_' .
            $this->classId . '_' .
            $this->quizId . '_' .
            $page;

        // 3. Asosiy so'rov (Optimallashtirilgan)
        $exams = Cache::remember($cacheKey, 300, function () use ($teacherId) {
            return Exam::with(['user', 'quiz.subject'])
                // DIQQAT: Javoblarni to'liq yuklash o'rniga faqat sonini olamiz (SQL COUNT)
                ->withCount('answers as total_questions')
                ->withCount(['answers as correct_answers' => function ($query) {
                    $query->whereHas('option', function ($q) {
                        $q->where('is_correct', 1);
                    });
                }])
                ->whereHas('quiz', function ($q) use ($teacherId) {
                    $q->where('created_by', $teacherId);
                })
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->whereHas('user', function ($userQuery) {
                            $userQuery->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%');
                        })
                            ->orWhereHas('quiz', function ($quizQuery) {
                                $quizQuery->where('name', 'like', '%' . $this->search . '%');
                            });
                    });
                })
                ->when($this->classId, function ($query) {
                    $query->whereHas('user', function ($q) {
                        // Agar bazada classes_id oddiy INT bo'lsa, pastdagi qatorni ishlating:
                        // $q->where('classes_id', $this->classId);

                        // Eski kodingizdagi JSON logikasi:
                        $q->whereJsonContains('classes_id', (string)$this->classId)
                            ->orWhereJsonContains('classes_id', (int)$this->classId);
                    });
                })
                ->when($this->quizId, function ($query) {
                    $query->where('quiz_id', $this->quizId);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        });

        return view('livewire.teacher.exam.exam-result-test', [
            'exams' => $exams,
            'classes' => $classes,
            'quizzes' => $quizzes
        ]);
    }
}
