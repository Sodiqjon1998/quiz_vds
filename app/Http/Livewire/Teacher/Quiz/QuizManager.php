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
use Maatwebsite\Excel\Facades\Excel; // Excel import uchun
use Smalot\PdfParser\Parser as PdfParser; // PDF import uchun

class QuizManager extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // === 1. GLOBAL STATE ===
    public $search = '';

    // === 2. QUIZ STATE ===
    public $showModal = false;
    public $showViewModal = false;
    public $isEdit = false;
    public $quizId, $name, $classes_id;
    public $viewingQuiz = null;

    // === 3. QUESTION STATE ===
    public $showQuestionsModal = false;
    public $showQuestionFormModal = false;
    public $currentQuiz = null;
    public $questionSearch = '';

    // Question Form Variables
    public $questionId, $questionText, $questionImage, $existingImage;
    public $options = ['', '', '', ''];
    public $correctOption = null; // 0, 1, 2, 3
    public $isEditQuestion = false;

    // === 4. ATTACHMENT STATE ===
    public $showAttachmentModal = false;
    public $attachmentQuizId, $attachmentDate, $attachmentTime, $attachmentNumber;

    // === 5. IMPORT STATE ===
    public $showImportModal = false;
    public $importFile;
    public $importClassId;

    // === LISTENERS ===
    protected $listeners = ['questionSaved' => '$refresh'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    // ==========================================
    // === 1. IMPORT FUNKSIYALARI (YANGI) ===
    // ==========================================

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
            'importFile' => 'required|file|mimes:xlsx,xls,pdf|max:10240', // Max 10MB
            'importClassId' => 'required|exists:classes,id',
        ], [
            'importFile.required' => 'Fayl tanlanmadi',
            'importFile.mimes' => 'Faqat Excel (.xlsx) yoki PDF fayl yuklang',
            'importClassId.required' => 'Sinfni tanlang',
        ]);

        $extension = $this->importFile->getClientOriginalExtension();
        $fileName = pathinfo($this->importFile->getClientOriginalName(), PATHINFO_FILENAME);

        DB::beginTransaction();
        try {
            // 1. Yangi Quiz yaratish
            $quiz = Quiz::create([
                'name' => $fileName,
                'subject_id' => Auth::user()->subject_id,
                'classes_id' => $this->importClassId,
                'status' => Quiz::STATUS_ACTIVE,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // 2. Fayl turiga qarab o'qish
            if (in_array($extension, ['xlsx', 'xls'])) {
                $this->processExcel($this->importFile->getRealPath(), $quiz->id);
            } elseif ($extension === 'pdf') {
                $this->processPdf($this->importFile->getRealPath(), $quiz->id);
            }

            DB::commit();
            session()->flash('message', 'Quiz muvaffaqiyatli import qilindi! Jami savollar: ' . $quiz->questions()->count());
            $this->closeImportModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Import xatoligi: ' . $e->getMessage());
        }
    }

    // Excel faylni qayta ishlash
    private function processExcel($filePath, $quizId)
    {
        // 1-qatorda sarlavha bor deb hisoblaymiz va uni tashlab yuboramiz
        $data = Excel::toCollection(new \stdClass, $filePath)->first()->slice(1);

        foreach ($data as $row) {
            // Bo'sh qatorlarni o'tkazib yuborish
            if (!isset($row[0]) || empty($row[0])) continue;

            $questionText = $row[0];
            $optA = $row[1];
            $optB = $row[2];
            $optC = $row[3];
            $optD = $row[4];
            $correctLetter = strtoupper(trim($row[5])); // A, B, C, D

            // Harfni indexga o'girish
            $correctMap = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
            $correctIndex = $correctMap[$correctLetter] ?? 0;

            // Savolni yaratish
            $question = Question::create([
                'quiz_id' => $quizId,
                'name' => $questionText,
                'status' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Variantlarni yaratish
            $optionsData = [$optA, $optB, $optC, $optD];
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

    // PDF faylni qayta ishlash
    private function processPdf($filePath, $quizId)
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        // PDF strukturasi murakkab bo'lgani uchun hozircha ogohlantirish beramiz
        // Kelajakda RegEx yordamida parsing yozish mumkin
        throw new \Exception("PDF import hozircha test rejimida. Iltimos, Excel formatidan foydalaning.");
    }

    // ==========================================
    // === 2. QUIZ CRUD (OPTIMALLASHTIRILGAN) ===
    // ==========================================

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
        ], [
            'name.required' => 'Quiz nomini kiriting',
            'classes_id.required' => 'Sinfni tanlang',
        ]);

        $data = [
            'name' => $this->name,
            'subject_id' => Auth::user()->subject_id,
            'classes_id' => $this->classes_id,
            'status' => Quiz::STATUS_ACTIVE,
        ];

        if ($this->isEdit) {
            Quiz::where('id', $this->quizId)->where('created_by', Auth::id())->update($data);
            session()->flash('message', 'Quiz muvaffaqiyatli yangilandi!');
        } else {
            $data['created_by'] = Auth::id();
            Quiz::create($data);
            session()->flash('message', 'Yangi quiz yaratildi!');
        }

        $this->showModal = false;
    }

    public function editQuiz($id)
    {
        $quiz = Quiz::where('id', $id)->where('created_by', Auth::id())->firstOrFail();
        $this->quizId = $quiz->id;
        $this->name = $quiz->name;
        $this->classes_id = $quiz->classes_id;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function deleteQuiz($id)
    {
        Quiz::where('id', $id)->where('created_by', Auth::id())->delete();
        session()->flash('message', 'Quiz o\'chirildi!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'classes_id', 'isEdit', 'quizId']);
    }

    // --- VIEW MODAL ---
    public function viewQuiz($id)
    {
        $this->viewingQuiz = Quiz::with(['questions.options', 'subject', 'class'])
            ->where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();
        $this->showViewModal = true;

        // Livewire 2 sintaksisi
        $this->dispatchBrowserEvent('renderMathJax');
    }



    public function formatMathForView($text)
    {
        // Agar allaqachon LaTeX formatida bo'lsa, tegmaymiz
        // \( yoki \[ yoki $$ borligini tekshirish
        if (str_contains($text, '\(') || str_contains($text, '\[') || str_contains($text, '$$')) {
            return $text;
        }

        // Faqat ^ belgisi bor matnlarni o'giramiz
        if (!str_contains($text, '^')) {
            return $text;
        }

        // Qavs ichidagi darajalarni o'girish: (5^3) → \( 5^3 \)
        $text = preg_replace('/\(([^)]*\^[^)]*)\)/', '\( $1 \)', $text);

        // Tenglamalarni o'girish: S=a^2 → \( S=a^2 \)
        // Lekin faqat LaTeX formatida bo'lmagan joylarni
        $text = preg_replace_callback('/([A-Za-z]+\s*=\s*[A-Za-z0-9\^\+\-\*\/\(\)\.]+)/', function ($matches) {
            // Agar allaqachon \( ichida bo'lmasa
            if (!str_contains($matches[0], '\(')) {
                return '\( ' . $matches[1] . ' \)';
            }
            return $matches[0];
        }, $text);

        // return $text;

        // Agar allaqachon LaTeX formatida bo'lsa
        if (str_contains($text, '\(') || str_contains($text, '\[')) {
            return $text;
        }

        // $ belgisi bor bo'lsa - bu matematika
        if (str_contains($text, '$')) {
            // $ ... $ → \( ... \)
            $text = preg_replace('/\$([^\$]+)\$/', '\( $1 \)', $text);
            // $$ ... $$ → \[ ... \]
            $text = preg_replace('/\$\$(.+?)\$\$/s', '\[ $1 \]', $text);
            return $text;
        }

        // LaTeX buyruqlari bor bo'lsa (frac, sqrt, cdot va boshqalar)
        if (preg_match('/\\\\(frac|sqrt|cdot|times|div|pm|geq|leq|neq|sum|prod|int|lim)/', $text)) {
            // Butun matnni LaTeX formatiga o'girish
            return '\( ' . $text . ' \)';
        }

        return $text;
    }


    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingQuiz = null;
    }

    // ==========================================
    // === 3. QUESTION CRUD (OPTIMALLASHTIRILGAN) ===
    // ==========================================

    public function manageQuestions($quizId)
    {
        $this->currentQuiz = Quiz::with(['subject', 'class'])->findOrFail($quizId);
        $this->showQuestionsModal = true;
        $this->questionSearch = '';
    }

    public function closeQuestionsModal()
    {
        $this->showQuestionsModal = false;
        $this->currentQuiz = null;
    }

    public function createQuestion()
    {
        $this->reset(['questionId', 'questionText', 'questionImage', 'existingImage', 'correctOption', 'isEditQuestion']);
        $this->options = ['', '', '', ''];
        $this->showQuestionFormModal = true;
    }

    public function closeQuestionFormModal()
    {
        $this->showQuestionFormModal = false;
        $this->reset(['questionId', 'questionText', 'questionImage', 'existingImage', 'correctOption', 'isEditQuestion']);
        $this->options = ['', '', '', ''];
    }

    public function saveQuestion()
    {
        $this->validate([
            'questionText' => 'required|min:5',
            'options.*' => 'required|min:1',
            'correctOption' => 'required|in:0,1,2,3',
            'questionImage' => 'nullable|image|max:2048',
        ], [
            'questionText.required' => 'Savol matnini kiriting',
            'options.*.required' => 'Barcha variantlarni to\'ldiring',
            'correctOption.required' => 'To\'g\'ri javobni belgilang',
        ]);

        DB::transaction(function () {
            $imagePath = $this->questionImage ? $this->questionImage->store('questions', 'public') : $this->existingImage;

            $data = [
                'name' => $this->questionText,
                'image' => $imagePath,
                'updated_by' => Auth::id()
            ];

            if ($this->isEditQuestion) {
                // Yangilash
                $question = Question::findOrFail($this->questionId);

                // Eski rasmni o'chirish (agar yangisi yuklansa)
                if ($this->questionImage && $question->image) {
                    Storage::disk('public')->delete($question->image);
                }

                $question->update($data);

                // Variantlarni tozalab, qayta yozamiz (osonroq yo'l)
                Option::where('question_id', $this->questionId)->delete();
                $qId = $this->questionId;
            } else {
                // Yaratish
                $data['quiz_id'] = $this->currentQuiz->id;
                $data['created_by'] = Auth::id();
                $data['status'] = 1;
                $question = Question::create($data);
                $qId = $question->id;
            }

            foreach ($this->options as $index => $optText) {
                Option::create([
                    'question_id' => $qId,
                    'name' => $optText,
                    'is_correct' => ($index == $this->correctOption),
                    'created_by' => Auth::id()
                ]);
            }
        });

        $this->closeQuestionFormModal();
        session()->flash('message', 'Savol muvaffaqiyatli saqlandi!'); // Global xabar
    }

    public function editQuestion($id)
    {
        $q = Question::with('options')->findOrFail($id);
        $this->questionId = $q->id;
        $this->questionText = $q->name;
        $this->existingImage = $q->image;

        $this->options = $q->options->pluck('name')->toArray();
        // Agar variantlar kam bo'lsa, 4 taga to'ldiramiz
        $this->options = array_pad($this->options, 4, '');

        $this->correctOption = $q->options->search(fn($o) => $o->is_correct);
        $this->isEditQuestion = true;
        $this->showQuestionFormModal = true;
    }

    public function deleteQuestion($id)
    {
        $q = Question::findOrFail($id);
        if ($q->image) {
            Storage::disk('public')->delete($q->image);
        }
        $q->delete();
        // session()->flash('message', 'Savol o\'chirildi!'); 
    }

    public function removeImage()
    {
        $this->existingImage = null;
        $this->questionImage = null;

        if ($this->isEditQuestion && $this->questionId) {
            $q = Question::find($this->questionId);
            if ($q && $q->image) {
                Storage::disk('public')->delete($q->image);
                $q->update(['image' => null]);
            }
        }
    }

    // ==========================================
    // === 4. ATTACHMENT (FAYLLAR) CRUD ===
    // ==========================================

    public function manageAttachments($quizId)
    {
        $this->attachmentQuizId = $quizId;
        $this->reset(['attachmentDate', 'attachmentTime', 'attachmentNumber']);
        $this->showAttachmentModal = true;
    }

    public function closeAttachmentModal()
    {
        $this->showAttachmentModal = false;
        $this->reset(['attachmentDate', 'attachmentTime', 'attachmentNumber']);
    }

    public function saveAttachment()
    {
        $this->validate([
            'attachmentDate' => 'required|date',
            'attachmentTime' => 'required',
            'attachmentNumber' => 'required|numeric',
        ]);

        DB::table('attachment')->insert([
            'quiz_id' => $this->attachmentQuizId,
            'date' => $this->attachmentDate,
            'time' => $this->attachmentTime,
            'number' => $this->attachmentNumber,
            'status' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->reset(['attachmentDate', 'attachmentTime', 'attachmentNumber']);
    }

    public function deleteAttachment($id)
    {
        DB::table('attachment')->where('id', $id)->delete();
    }

    // Computed Property: Fayllarni olish
    public function getAttachmentsProperty()
    {
        if (!$this->attachmentQuizId) return [];
        return DB::table('attachment')
            ->where('quiz_id', $this->attachmentQuizId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // ==========================================
    // === 5. RENDER & GETTERS ===
    // ==========================================

    public function render()
    {
        $quizzes = Quiz::with(['subject', 'class', 'attachment'])
            ->withCount('questions')
            ->where('created_by', Auth::id())
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.teacher.quiz.quiz-manager', [
            'quizzes' => $quizzes,
            'classes' => Classes::all(),
        ]);
    }

    // Savollarni olish (Computed - optimallik uchun)
    public function getQuestionsProperty()
    {
        if (!$this->currentQuiz) return [];
        return $this->currentQuiz->questions()
            ->with('options')
            ->where('name', 'like', '%' . $this->questionSearch . '%')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
