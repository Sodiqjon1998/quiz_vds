<?php

namespace App\Http\Livewire\Teacher\Exam;

use App\Models\Exam;
use App\Models\Teacher\Quiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ExamResultTest extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    // Sinf bo'yicha filter uchun o'zgaruvchi
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

    // YANGI: Sinf o'zgarganda sahifani yangilash
    public function updatingClassId()
    {
        $this->resetPage();
    }

    public function showDetails($examId)
    {
        try {
            Log::info('showDetails called with exam ID: ' . $examId);

            // Relation nomlarini to'g'riladik
            $this->selectedExam = Exam::with([
                'user',  // 'users' emas
                'quiz.subject',
                'answers.question',  // 'exam_answer' emas
                'answers.option'
            ])->findOrFail($examId);

            Log::info('Exam loaded', [
                'exam_id' => $this->selectedExam->id,
                'answers_count' => $this->selectedExam->answers->count()
            ]);

            // Statistikani hisoblash
            $totalQuestions = $this->selectedExam->answers->count();
            $correctAnswers = 0;

            foreach ($this->selectedExam->answers as $answer) {
                if ($answer->option && $answer->option->is_correct) {
                    $correctAnswers++;
                }
            }

            $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

            $this->examStats = [
                'total' => $totalQuestions,
                'correct' => $correctAnswers,
                'percentage' => $percentage
            ];

            $this->showDetailModal = true;

            Log::info('Modal should be open now', [
                'showDetailModal' => $this->showDetailModal,
                'stats' => $this->examStats
            ]);
        } catch (\Exception $e) {
            Log::error('Error in showDetails: ' . $e->getMessage(), [
                'exam_id' => $examId,
                'trace' => $e->getTraceAsString()
            ]);

            $this->showDetailModal = false;
            session()->flash('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function closeDetailModal()
    {
        Log::info('closeDetailModal called');
        $this->showDetailModal = false;
        $this->selectedExam = null;
        $this->examStats = [];
    }

    public function render()
    {
        // Sinflar ro'yxati (Dropdown uchun)
        $classes = DB::table('classes')->orderBy('name')->get();

        // 3. Quizlar ro'yxati (Faqat shu o'qituvchi tuzganlari)
        $quizzes = Quiz::where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Asosiy so'rov
        $exams = Exam::with(['user', 'quiz.subject', 'answers.option'])
            ->whereHas('quiz', function ($q) {
                $q->where('created_by', Auth::id());
            })
            // Qidiruv
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
            // Sinf Filter
            ->when($this->classId, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->whereJsonContains('classes_id', (string)$this->classId)
                        ->orWhereJsonContains('classes_id', (int)$this->classId);
                });
            })
            // 4. QUIZ FILTER (YANGI)
            ->when($this->quizId, function ($query) {
                $query->where('quiz_id', $this->quizId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.teacher.exam.exam-result-test', [
            'exams' => $exams,
            'classes' => $classes,
            'quizzes' => $quizzes // <--- Viewga yuboramiz
        ]);
    }
}
