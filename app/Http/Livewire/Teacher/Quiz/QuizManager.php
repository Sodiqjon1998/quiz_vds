<?php

namespace App\Http\Livewire\Teacher\Quiz;

use App\Models\Subjects;
use App\Models\Classes;
use App\Models\Teacher\Quiz;
use App\Models\Teacher\Question;
use App\Models\Option;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuizManager extends Component
{
    use WithPagination, WithFileUploads;

    // === Quiz ===
    public $search = '';
    public $quizId;
    public $name, $subject_id, $classes_id;
    public $isEdit = false;
    public $showModal = false;

    // === View Modal ===
    public $showViewModal = false;
    public $viewingQuiz = null;

    // === Question Management ===
    public $showQuestionsModal = false;
    public $showQuestionFormModal = false;
    public $currentQuiz = null;

    public $questionId;
    public $questionText = '';
    public $questionImage = null; // Yangi: savol uchun rasm
    public $existingImage = null; // Mavjud rasm
    public $options = ['', '', '', ''];
    public $correctOption = null;
    public $isEditQuestion = false;
    public $questionSearch = '';

    protected $paginationTheme = 'bootstrap';

    // === VALIDATION RULES ===
    protected function rules()
    {
        $rules = [
            'name' => 'required|min:3',
            'subject_id' => 'required|exists:subjects,id',
            'classes_id' => 'required|exists:classes,id',
        ];

        if ($this->showQuestionFormModal) {
            $rules = array_merge($rules, [
                'questionText' => 'required|min:5',
                'questionImage' => 'nullable|image|max:2048', // Max 2MB
                'options.0' => 'required|min:1',
                'options.1' => 'required|min:1',
                'options.2' => 'required|min:1',
                'options.3' => 'required|min:1',
                'correctOption' => 'required|in:0,1,2,3',
            ]);
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Quiz nomini kiritish majburiy',
        'name.min' => 'Quiz nomi kamida 3 ta belgidan iborat bo\'lishi kerak',
        'subject_id.required' => 'Fanni tanlash majburiy',
        'classes_id.required' => 'Sinfni tanlash majburiy',

        'questionText.required' => 'Savol matnini kiriting',
        'questionText.min' => 'Savol kamida 5 ta belgidan iborat bo\'lishi kerak',
        'questionImage.image' => 'Faqat rasm fayllarini yuklash mumkin',
        'questionImage.max' => 'Rasm hajmi 2MB dan oshmasligi kerak',
        'options.0.required' => 'A variantini to\'ldiring',
        'options.1.required' => 'B variantini to\'ldiring',
        'options.2.required' => 'C variantini to\'ldiring',
        'options.3.required' => 'D variantini to\'ldiring',
        'correctOption.required' => 'To\'g\'ri javobni tanlang',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ======================
    // === QUIZ CRUD ===
    // ======================

    public function createQuiz()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function saveQuiz()
    {
        $this->validate();

        if ($this->isEdit) {
            $quiz = Quiz::where('id', $this->quizId)
                ->where('created_by', Auth::id())
                ->firstOrFail();

            $quiz->update([
                'name' => $this->name,
                'subject_id' => $this->subject_id,
                'classes_id' => $this->classes_id,
                'status' => Quiz::STATUS_ACTIVE,
                'updated_by' => Auth::id(),
            ]);

            session()->flash('message', 'Quiz muvaffaqiyatli yangilandi!');
        } else {
            Quiz::create([
                'name' => $this->name,
                'subject_id' => $this->subject_id,
                'classes_id' => $this->classes_id,
                'status' => Quiz::STATUS_ACTIVE,
                'created_by' => Auth::id(),
            ]);

            session()->flash('message', 'Yangi quiz qo\'shildi!');
        }

        $this->resetInputFields();
        $this->showModal = false;
    }

    public function editQuiz($id)
    {
        $quiz = Quiz::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        $this->quizId = $quiz->id;
        $this->name = $quiz->name;
        $this->subject_id = $quiz->subject_id;
        $this->classes_id = $quiz->classes_id;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function deleteQuiz($id)
    {
        $quiz = Quiz::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        $quiz->delete();
        session()->flash('message', 'Quiz o\'chirildi!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function viewQuiz($id)
    {
        $this->viewingQuiz = Quiz::where('id', $id)
            ->where('created_by', Auth::id())
            ->with(['questions.options'])
            ->firstOrFail();

        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingQuiz = null;
    }

    // ======================
    // === QUESTION MANAGEMENT ===
    // ======================

    public function manageQuestions($quizId)
    {
        $this->currentQuiz = Quiz::where('id', $quizId)
            ->where('created_by', Auth::id())
            ->with(['subject', 'class'])
            ->firstOrFail();

        $this->showQuestionsModal = true;
        $this->questionSearch = '';
    }

    public function closeQuestionsModal()
    {
        $this->showQuestionsModal = false;
        $this->currentQuiz = null;
        $this->questionSearch = '';
    }

    public function createQuestion()
    {
        $this->resetQuestionFields();
        $this->showQuestionFormModal = true;
    }

    public function closeQuestionFormModal()
    {
        $this->showQuestionFormModal = false;
        $this->resetQuestionFields();
    }

    public function saveQuestion()
    {
//        $this->validate();

        if ($this->correctOption === null || $this->correctOption === '') {
            session()->flash('question_error', 'To\'g\'ri javobni tanlang!');
            return;
        }

        $correctOption = (int) $this->correctOption;

        DB::beginTransaction();
        try {
            // Rasmni yuklash
            $imagePath = null;
            if ($this->questionImage) {
                $imagePath = $this->questionImage->store('questions', 'public');
            }

            if ($this->isEditQuestion) {
                $question = Question::findOrFail($this->questionId);

                // Eski rasmni o'chirish
                if ($imagePath && $question->image) {
                    Storage::disk('public')->delete($question->image);
                }

                $question->update([
                    'name' => $this->questionText,
                    'image' => $imagePath ?? $question->image,
                    'updated_by' => Auth::id(),
                ]);

                Option::where('question_id', $question->id)->delete();
            } else {
                $question = Question::create([
                    'quiz_id' => $this->currentQuiz->id,
                    'name' => $this->questionText,
                    'image' => $imagePath,
                    'status' => 1,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            // Variantlarni qo'shish
            foreach ($this->options as $index => $optionText) {
                $trimmedText = trim($optionText);

                if (empty($trimmedText)) {
                    DB::rollBack();
                    session()->flash('question_error', 'Variant ' . chr(65 + $index) . ' bo\'sh!');
                    return;
                }

                Option::create([
                    'question_id' => $question->id,
                    'name' => $trimmedText,
                    'is_correct' => ($index === $correctOption) ? 1 : 0,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            DB::commit();

            session()->flash('question_message', $this->isEditQuestion ? 'Savol yangilandi!' : 'Savol qo\'shildi!');

            $this->resetQuestionFields();
            $this->showQuestionFormModal = false;

            $this->currentQuiz->refresh();
            $this->currentQuiz->load(['subject', 'class']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Question save error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            session()->flash('question_error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function editQuestion($id)
    {
        $question = Question::with('options')->findOrFail($id);

        $this->resetQuestionFields();

        $this->questionId = $question->id;
        $this->questionText = $question->name;
        $this->existingImage = $question->image;
        $this->isEditQuestion = true;

        $options = $question->options->sortBy('id')->values();

        foreach ($options as $index => $option) {
            if ($index < 4) {
                $this->options[$index] = $option->name;
                if ($option->is_correct) {
                    $this->correctOption = $index;
                }
            }
        }

        $this->showQuestionFormModal = true;
    }

    public function deleteQuestion($id)
    {
        $question = Question::where('id', $id)
            ->whereHas('quiz', fn($q) => $q->where('created_by', Auth::id()))
            ->firstOrFail();

        // Rasmni o'chirish
        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }

        $question->delete();
        session()->flash('question_message', 'Savol o\'chirildi!');

        $this->currentQuiz->refresh();
        $this->currentQuiz->load(['subject', 'class']);
    }

    public function removeImage()
    {
        $this->existingImage = null;
        $this->questionImage = null;

        if ($this->isEditQuestion && $this->questionId) {
            $question = Question::find($this->questionId);
            if ($question && $question->image) {
                Storage::disk('public')->delete($question->image);
                $question->update(['image' => null]);
            }
        }
    }

    // ======================
    // === RESET FIELDS ===
    // ======================

    private function resetInputFields()
    {
        $this->quizId = null;
        $this->name = '';
        $this->subject_id = null;
        $this->classes_id = null;
        $this->isEdit = false;
    }

    private function resetQuestionFields()
    {
        $this->questionId = null;
        $this->questionText = '';
        $this->questionImage = null;
        $this->existingImage = null;
        $this->options = ['', '', '', ''];
        $this->correctOption = null;
        $this->isEditQuestion = false;
        $this->resetValidation();
    }

    // ======================
    // === GETTERS ===
    // ======================

    public function getQuestionsProperty()
    {
        if (!$this->currentQuiz || $this->currentQuiz->created_by != Auth::id()) {
            return collect();
        }

        return Question::with('options')
            ->where('quiz_id', $this->currentQuiz->id)
            ->when($this->questionSearch, fn($q) => $q->where('name', 'like', '%' . $this->questionSearch . '%'))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // ======================
    // === RENDER ===
    // ======================

    public function render()
    {
        $userId = Auth::id();

        $quizzes = Quiz::with(['subject', 'class', 'creator'])
            ->withCount('questions')
            ->where('created_by', $userId)
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.teacher.quiz.quiz-manager', [
            'quizzes' => $quizzes,
            'subjects' => Subjects::all(),
            'classes' => Classes::all(),
        ]);
    }
}
