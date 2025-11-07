<?php

namespace App\Http\Livewire\Teacher\Quiz;

use App\Models\Teacher\Quiz;
use App\Models\Teacher\Question;
use App\Models\Teacher\Option;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionManager extends Component
{
    use WithPagination;

    public $quizId;
    public $quiz;
    public $search = '';
    public $questionId;
    public $questionText;
    public $options = ['', '', '', ''];
    public $correctOption = null;
    public $isEdit = false;
    public $showModal = false;

    protected $paginationTheme = 'bootstrap';

    public function mount($quizId)
    {
        $this->quizId = $quizId;
        $this->quiz = Quiz::with(['subject', 'class'])->findOrFail($quizId);
    }

    protected function rules()
    {
        return [
            'questionText' => 'required|min:5',
            'options.0' => 'required|min:1',
            'options.1' => 'required|min:1',
            'options.2' => 'required|min:1',
            'options.3' => 'required|min:1',
            'correctOption' => 'required|in:0,1,2,3',
        ];
    }

    protected $messages = [
        'questionText.required' => 'Savol matnini kiriting',
        'questionText.min' => 'Savol kamida 5 ta belgidan iborat bo\'lishi kerak',
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

    public function saveQuestion()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            if ($this->isEdit) {
                $question = Question::find($this->questionId);
                $question->name = $this->questionText;
                $question->updated_by = Auth::id();
                $question->save();

                $question->options()->delete();
            } else {
                $question = Question::create([
                    'quiz_id' => $this->quizId,
                    'name' => $this->questionText,
                    'status' => Question::STATUS_ACTIVE,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            foreach ($this->options as $index => $optionText) {
                Option::create([
                    'question_id' => $question->id,
                    'name' => $optionText,
                    'is_correct' => ($index == $this->correctOption) ? 1 : 0,
                    'status' => Option::STATUS_ACTIVE,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            DB::commit();
            session()->flash('message', $this->isEdit ? 'Savol yangilandi!' : 'Savol qo\'shildi!');
            $this->resetInputFields();
            $this->showModal = false;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function editQuestion($id)
    {
        $question = Question::with('options')->findOrFail($id);
        $this->questionId = $question->id;
        $this->questionText = $question->name;

        foreach ($question->options as $index => $option) {
            $this->options[$index] = $option->name;
            if ($option->is_correct) {
                $this->correctOption = $index;
            }
        }

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function deleteQuestion($id)
    {
        Question::find($id)->delete();
        session()->flash('message', 'Savol o\'chirildi!');
    }

    public function createQuestion()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->questionId = null;
        $this->questionText = '';
        $this->options = ['', '', '', ''];
        $this->correctOption = null;
        $this->isEdit = false;
    }

    public function render()
    {
        $questions = Question::with('options')
            ->where('quiz_id', $this->quizId)
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.teacher.quiz.question-manager', [
            'questions' => $questions
        ]);
    }
}
