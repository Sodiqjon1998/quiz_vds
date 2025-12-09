<?php

namespace App\Http\Livewire\Teacher\Quiz;

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
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Smalot\PdfParser\Parser as PdfParser;

class QuizManager extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // === GLOBAL STATE ===
    public $search = '';

    // === QUIZ STATE ===
    public $showModal = false;
    public $showViewModal = false;
    public $isEdit = false;
    public $quizId, $name, $classes_id;
    public $viewingQuiz = null;

    // === QUESTION STATE ===
    public $showQuestionsModal = false;
    public $showQuestionFormModal = false;
    public $currentQuiz = null;
    public $questionSearch = '';

    // Question Form Variables
    public $questionId, $questionText, $questionImage, $existingImage;
    public $options = ['', '', '', ''];
    public $correctOption = null;
    public $isEditQuestion = false;

    // === ATTACHMENT STATE ===
    public $showAttachmentModal = false;
    public $attachmentQuizId, $attachmentDate, $attachmentTime, $attachmentNumber;

    // === IMPORT STATE ===
    public $showImportModal = false;
    public $importFile;
    public $importClassId;

    protected $listeners = ['questionSaved' => '$refresh'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    // === IMPORT FUNKSIYALARI ===
    public function openImportModal()
    {
        $this->reset(['importFile', 'importClassId']);
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
    }

    public function importQuiz()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,pdf|max:10240',
            'importClassId' => 'required|exists:classes,id',
        ]);

        $extension = $this->importFile->getClientOriginalExtension();
        $fileName = pathinfo($this->importFile->getClientOriginalName(), PATHINFO_FILENAME);
        $fullPath = $this->importFile->getRealPath();

        DB::beginTransaction();
        try {
            $quiz = Quiz::create([
                'name' => $fileName,
                'subject_id' => Auth::user()->subject_id,
                'classes_id' => $this->importClassId,
                'status' => Quiz::STATUS_ACTIVE,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            if (in_array($extension, ['xlsx', 'xls'])) {
                $this->processExcel($fullPath, $quiz->id);
            }

            DB::commit();
            session()->flash('message', "✅ Quiz muvaffaqiyatli import qilindi!");
            $this->closeImportModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Import xatoligi: ' . $e->getMessage());
        }
    }

    private function processExcel($filePath, $quizId)
    {
        $data = Excel::toCollection(new \stdClass, $filePath)->first()->slice(1);
        foreach ($data as $row) {
            if (!isset($row[0]) || empty($row[0])) continue;

            $correctMap = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
            $correctLetter = strtoupper(trim($row[5]));
            $correctIndex = $correctMap[$correctLetter] ?? 0;

            $question = Question::create([
                'quiz_id' => $quizId,
                'name' => $row[0],
                'status' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $optionsData = [$row[1], $row[2], $row[3], $row[4]];
            foreach ($optionsData as $index => $optText) {
                Option::create([
                    'question_id' => $question->id,
                    'name' => $optText ?? '',
                    'is_correct' => ($index === $correctIndex),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }
    }

    // === QUIZ CRUD ===
    public function createQuiz()
    {
        $this->reset(['name', 'classes_id', 'quizId', 'isEdit']);
        $this->showModal = true;
    }

    public function saveQuiz()
    {
        $this->validate([
            'name' => 'required|min:3',
            'classes_id' => 'required|exists:classes,id',
        ]);

        $data = [
            'name' => $this->name,
            'subject_id' => Auth::user()->subject_id,
            'classes_id' => $this->classes_id,
            'status' => Quiz::STATUS_ACTIVE,
        ];

        if ($this->isEdit) {
            Quiz::where('id', $this->quizId)->update($data);
        } else {
            $data['created_by'] = Auth::id();
            Quiz::create($data);
        }
        $this->showModal = false;
        session()->flash('message', 'Muvaffaqiyatli saqlandi!');
    }

    public function editQuiz($id)
    {
        $quiz = Quiz::findOrFail($id);
        $this->quizId = $quiz->id;
        $this->name = $quiz->name;
        $this->classes_id = $quiz->classes_id;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function deleteQuiz($id)
    {
        Quiz::where('id', $id)->delete();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    // === VIEW MODAL ===
    public function viewQuiz($id)
    {
        $this->viewingQuiz = Quiz::with(['questions.options', 'subject', 'class'])
            ->findOrFail($id);
        $this->showViewModal = true;
        // MUHIM: Modal ochilganda MathJax render bo'lishi uchun signal yuboramiz
        $this->dispatchBrowserEvent('renderMathJax');
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
    }

    // === QUESTION CRUD ===
    public function manageQuestions($quizId)
    {
        $this->currentQuiz = Quiz::with(['subject', 'class'])->findOrFail($quizId);
        $this->showQuestionsModal = true;
        $this->dispatchBrowserEvent('renderMathJax');
    }

    public function closeQuestionsModal()
    {
        $this->showQuestionsModal = false;
    }

    public function createQuestion()
    {
        $this->reset(['questionId', 'questionText', 'questionImage', 'existingImage', 'correctOption', 'isEditQuestion']);
        $this->options = ['', '', '', ''];
        $this->showQuestionFormModal = true;
        $this->dispatchBrowserEvent('renderMathJax');
    }

    public function closeQuestionFormModal()
    {
        $this->showQuestionFormModal = false;
    }

    public function saveQuestion()
    {
        $this->validate([
            'questionText' => 'required',
            'options.*' => 'required',
            'correctOption' => 'required',
        ]);

        DB::transaction(function () {
            $imagePath = $this->questionImage ? $this->questionImage->store('questions', 'public') : $this->existingImage;

            if ($this->isEditQuestion) {
                $question = Question::findOrFail($this->questionId);
                $question->update(['name' => $this->questionText, 'image' => $imagePath]);
                Option::where('question_id', $this->questionId)->delete();
                $qId = $this->questionId;
            } else {
                $question = Question::create([
                    'quiz_id' => $this->currentQuiz->id,
                    'name' => $this->questionText,
                    'image' => $imagePath,
                    'created_by' => Auth::id(),
                    'status' => 1
                ]);
                $qId = $question->id;
            }

            foreach ($this->options as $index => $optText) {
                Option::create([
                    'question_id' => $qId,
                    'name' => $optText,
                    'is_correct' => ($index == $this->correctOption),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id()
                ]);
            }
        });

        $this->closeQuestionFormModal();
        session()->flash('message', 'Savol saqlandi!');
        $this->dispatchBrowserEvent('renderMathJax');
    }

    public function editQuestion($id)
    {
        $q = Question::with('options')->findOrFail($id);
        $this->questionId = $q->id;
        $this->questionText = $q->name;
        $this->existingImage = $q->image;
        $this->options = $q->options->pluck('name')->toArray();
        $this->options = array_pad($this->options, 4, '');
        $this->correctOption = $q->options->search(fn($o) => $o->is_correct);
        $this->isEditQuestion = true;
        $this->showQuestionFormModal = true;
        $this->dispatchBrowserEvent('renderMathJax');
    }

    public function deleteQuestion($id)
    {
        Question::findOrFail($id)->delete();
    }

    public function removeImage()
    {
        if ($this->questionId) {
            Question::where('id', $this->questionId)->update(['image' => null]);
        }
        $this->existingImage = null;
        $this->questionImage = null;
    }

    // === ATTACHMENT ===
    public function manageAttachments($quizId)
    {
        $this->attachmentQuizId = $quizId;
        $this->showAttachmentModal = true;
    }

    public function closeAttachmentModal()
    {
        $this->showAttachmentModal = false;
    }

    public function saveAttachment()
    {
        $this->validate(['attachmentDate' => 'required', 'attachmentNumber' => 'required']);
        DB::table('attachment')->insert([
            'quiz_id' => $this->attachmentQuizId,
            'date' => $this->attachmentDate,
            'time' => $this->attachmentTime,
            'number' => $this->attachmentNumber,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
        $this->reset(['attachmentDate', 'attachmentTime', 'attachmentNumber']);
    }

    public function deleteAttachment($id)
    {
        DB::table('attachment')->where('id', $id)->delete();
    }

    public function getAttachmentsProperty()
    {
        if (!$this->attachmentQuizId) return [];
        return DB::table('attachment')->where('quiz_id', $this->attachmentQuizId)->get();
    }

    // === RENDER ===
    public function render()
    {
        $classes = Cache::remember('classes_list', 3600, fn() => Classes::all());

        $quizzes = Quiz::with(['subject', 'class'])
            ->withCount('questions')
            ->where('created_by', Auth::id())
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.teacher.quiz.quiz-manager', [
            'quizzes' => $quizzes,
            'classes' => $classes,
        ]);
    }

    public function getQuestionsProperty()
    {
        if (!$this->currentQuiz) return [];
        return $this->currentQuiz->questions()
            ->with('options')
            ->where('name', 'like', '%' . $this->questionSearch . '%')
            ->get();
    }
}
