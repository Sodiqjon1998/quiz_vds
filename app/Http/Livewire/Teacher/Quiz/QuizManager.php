<?php
//
//namespace App\Http\Livewire\Teacher\Quiz;
//
//use App\Models\Subjects;
//use App\Models\Classes;
//use App\Models\Teacher\Quiz;
//use Livewire\Component;
//use Livewire\WithPagination;
//use Illuminate\Support\Facades\Auth;
//
//class QuizManager extends Component
//{
//    use WithPagination;
//
//    public $search = '';
//    public $quizId;
//    public $name, $subject_id, $classes_id, $status;
//    public $isEdit = false;
//    public $showModal = false;
//    public $showViewModal = false;
//    public $viewingQuiz = null;
//
//    protected $paginationTheme = 'bootstrap';
//
//    protected function rules()
//    {
//        return [
//            'name' => 'required|min:3',
//            'subject_id' => 'required|exists:subjects,id',
//            'classes_id' => 'required|exists:classes,id',
//        ];
//    }
//
//    protected $messages = [
//        'name.required' => 'Quiz nomini kiritish majburiy',
//        'name.min' => 'Quiz nomi kamida 3 ta belgidan iborat bo\'lishi kerak',
//        'subject_id.required' => 'Fanni tanlash majburiy',
//        'classes_id.required' => 'Sinfni tanlash majburiy',
//    ];
//
//    public function updated($propertyName)
//    {
//        $this->validateOnly($propertyName);
//    }
//
//    public function updatingSearch()
//    {
//        $this->resetPage();
//    }
//
//    public function saveQuiz()
//    {
//        $this->validate();
//
//        if ($this->isEdit) {
//            $quiz = Quiz::find($this->quizId);
//            $quiz->name = $this->name;
//            $quiz->subject_id = $this->subject_id;
//            $quiz->classes_id = $this->classes_id;
//            $quiz->status = Quiz::STATUS_ACTIVE;
//            $quiz->updated_by = Auth::id();
//            $quiz->save();
//
//            session()->flash('message', 'Quiz muvaffaqiyatli yangilandi!');
//        } else {
//            Quiz::create([
//                'name' => $this->name,
//                'subject_id' => $this->subject_id,
//                'classes_id' => $this->classes_id,
//                'status' => Quiz::STATUS_ACTIVE,
//                'created_by' => Auth::id(),
//            ]);
//
//            session()->flash('message', 'Yangi quiz qo\'shildi!');
//        }
//
//        $this->resetInputFields();
//        $this->showModal = false;
//    }
//
//    public function editQuiz($id)
//    {
//        $quiz = Quiz::findOrFail($id);
//        $this->quizId = $quiz->id;
//        $this->name = $quiz->name;
//        $this->subject_id = $quiz->subject_id;
//        $this->classes_id = $quiz->classes_id;
//        $this->isEdit = true;
//        $this->showModal = true;
//    }
//
//    public function deleteQuiz($id)
//    {
//        Quiz::find($id)->delete();
//        session()->flash('message', 'Quiz o\'chirildi!');
//    }
//
//    public function createQuiz()
//    {
//        $this->resetInputFields();
//        $this->showModal = true;
//    }
//
//    public function closeModal()
//    {
//        $this->showModal = false;
//        $this->resetInputFields();
//    }
//
//    public function viewQuiz($id)
//    {
//        $this->viewingQuiz = Quiz::findOrFail($id);
//        $this->showViewModal = true;
//    }
//
//    public function closeViewModal()
//    {
//        $this->showViewModal = false;
//        $this->viewingQuiz = null;
//    }
//
//    private function resetInputFields()
//    {
//        $this->quizId = null;
//        $this->name = '';
//        $this->subject_id = null;
//        $this->classes_id = null;
//        $this->isEdit = false;
//    }
//
//    public function render()
//    {
//        $quizzes = Quiz::with(['subject', 'class', 'creator'])
//            ->withCount('questions')
//            ->where('name', 'like', '%' . $this->search . '%')
//            ->orderBy('created_at', 'desc')
//            ->paginate(10);
//
//        return view('livewire.teacher.quiz.quiz-manager', [
//            'quizzes' => $quizzes,
//            'subjects' => Subjects::all(),
//            'classes' => Classes::all(),
//        ]);
//    }
//}


namespace App\Http\Livewire\Teacher\Quiz;

use App\Models\Subjects;
use App\Models\Classes;
use App\Models\Teacher\Quiz;
use App\Models\Teacher\Question;
use App\Models\Option;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizManager extends Component
{
    use WithPagination;

    public $search = '';
    public $quizId;
    public $name, $subject_id, $classes_id, $status;
    public $isEdit = false;
    public $showModal = false;
    public $showViewModal = false;
    public $viewingQuiz = null;

    // Question management
    public $showQuestionsModal = false;
    public $showQuestionFormModal = false;
    public $currentQuiz = null;
    public $questionId;
    public $questionText;
    public $options = ['', '', '', ''];
    public $correctOption = null;
    public $isEditQuestion = false;
    public $questionSearch = '';

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        $rules = [
            'name' => 'required|min:3',
            'subject_id' => 'required|exists:subjects,id',
            'classes_id' => 'required|exists:classes,id',
        ];

        if ($this->showQuestionFormModal) {
            $rules = [
                'questionText' => 'required|min:5',
                'options.0' => 'required|min:1',
                'options.1' => 'required|min:1',
                'options.2' => 'required|min:1',
                'options.3' => 'required|min:1',
                'correctOption' => 'required|in:0,1,2,3',
            ];
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

    // Quiz CRUD
    public function saveQuiz()
    {
        $this->validate();

        if ($this->isEdit) {
            $quiz = Quiz::find($this->quizId);
            $quiz->name = $this->name;
            $quiz->subject_id = $this->subject_id;
            $quiz->classes_id = $this->classes_id;
            $quiz->status = Quiz::STATUS_ACTIVE;
            $quiz->updated_by = Auth::id();
            $quiz->save();

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

    public function createQuiz()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function viewQuiz($id)
    {
        $quiz = Quiz::where('id', $id)
            ->where('created_by', Auth::id())
            ->with(['questions.options'])
            ->firstOrFail();

        $this->viewingQuiz = $quiz;
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingQuiz = null;
    }

    // Question Management
    public function manageQuestions($quizId)
    {
        $quiz = Quiz::where('id', $quizId)
            ->where('created_by', Auth::id()) // O'Z QUIZI EMASMI?
            ->with(['subject', 'class'])
            ->firstOrFail();

        $this->currentQuiz = $quiz;
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

    public function saveQuestion()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            if ($this->isEditQuestion) {
                $question = Question::find($this->questionId);
                $question->name = $this->questionText;
                $question->updated_by = Auth::id();
                $question->save();

                // MUHIM: ESKI VARIANTLARNI O'CHIRISH
                $question->options()->delete();
            } else {
                $question = Question::create([
                    'quiz_id' => $this->currentQuiz->id,
                    'name' => $this->questionText,
                    'status' => Question::STATUS_ACTIVE,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            // YANGI VARIANTLARNI QO'SHISH
            foreach ($this->options as $index => $optionText) {
                if (!empty($optionText)) { // Bo'sh variantlarni o'tkazib yuborish
                    Option::create([
                        'question_id' => $question->id,
                        'name' => $optionText,
                        'is_correct' => ($index == $this->correctOption) ? 1 : 0,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();
            session()->flash('question_message', $this->isEditQuestion ? 'Savol yangilandi!' : 'Savol qo\'shildi!');

            $this->resetQuestionFields();
            $this->showQuestionFormModal = false;

            // YANGI MA'LUMOTLARNI YUKLASH
            $this->currentQuiz = Quiz::with(['subject', 'class'])->findOrFail($this->currentQuiz->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('question_error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function editQuestion($id)
    {
        $question = Question::with('options')->findOrFail($id);

        $this->resetQuestionFields();

        $this->questionId = $question->id;
        $this->questionText = $question->name;
        $this->isEditQuestion = true;
        $this->showQuestionFormModal = true;

        // Variantlarni to'g'ri tartibda yuklash
        foreach ($question->options as $option) {
            // Tartib bo'yicha emas, balki DB'dagi tartib bo'yicha
            // Lekin biz A=0, B=1, C=2, D=3 deb belgilaymiz
            $index = $option->id; // Xato emas, lekin tartibni saqlash kerak
            // To'g'ri: tartibni DB'da saqlash kerak (masalan, `order` field)
            // Hozircha: birinchi kelgan birinchi index
        }

        // YAXSHIROQ: DB'da `order` field qo'shing yoki tartibni saqlang
        // Yoki hozircha: tartibni qo'lda belgilang
        $options = $question->options->sortBy('created_at'); // yoki created_at
        foreach ($options as $index => $option) {
            if ($index < 4) {
                $this->options[$index] = $option->name;
                if ($option->is_correct) {
                    $this->correctOption = $index;
                }
            }
        }
    }

    public function deleteQuestion($id)
    {
        $question = Question::where('id', $id)
            ->whereHas('quiz', function($q) {
                $q->where('created_by', Auth::id());
            })
            ->firstOrFail();

        $question->delete();
        session()->flash('question_message', 'Savol o\'chirildi!');
        $this->currentQuiz = Quiz::with(['subject', 'class'])->findOrFail($this->currentQuiz->id);
    }

    public function closeQuestionFormModal()
    {
        $this->showQuestionFormModal = false;
        $this->resetQuestionFields();
    }

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
        $this->options = ['', '', '', '']; // Har doim 4 ta
        $this->correctOption = null;
        $this->isEditQuestion = false;
    }

    public function getQuestionsProperty()
    {
        if (!$this->currentQuiz || $this->currentQuiz->created_by != Auth::id()) {
            return collect(); // Ruxsatsiz → bo'sh
        }

        return Question::with('options')
            ->where('quiz_id', $this->currentQuiz->id)
            ->where('name', 'like', '%' . $this->questionSearch . '%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        $userId = Auth::id(); // Joriy o'qituvchi ID

        $quizzes = Quiz::with(['subject', 'class', 'creator'])
            ->withCount('questions')
            ->where('name', 'like', '%' . $this->search . '%')
            ->where('created_by', $userId) // FAQAT O'Z QUIZLAR!
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.teacher.quiz.quiz-manager', [
            'quizzes' => $quizzes,
            'subjects' => Subjects::all(),
            'classes' => Classes::all(),
        ]);
    }
}
