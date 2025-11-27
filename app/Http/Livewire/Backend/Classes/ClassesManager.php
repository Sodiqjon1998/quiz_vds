<?php

namespace App\Http\Livewire\Backend\Classes;

use App\Models\Classes;
use App\Models\Users;
use Livewire\Component;
use Livewire\WithPagination;

class ClassesManager extends Component
{
    use WithPagination;

    // Properties
    public $search = '';
    public $classId;
    public $name, $status;

    // Yangi qo'shilgan maydonlar
    public $telegram_chat_id;
    public $telegram_topic_id;

    public $isEdit = false;
    public $showModal = false;
    public $showViewModal = false;
    public $viewingClass = null;

    // Students pagination
    public $studentsPage = 1;
    public $studentsPerPage = 5;

    protected $paginationTheme = 'bootstrap';

    // Validation rules
    protected function rules()
    {
        return [
            'name' => 'required|min:2|unique:classes,name,' . $this->classId,
            'telegram_chat_id' => 'required|string|max:30',
            'telegram_topic_id' => 'required|string|max:30',
        ];
    }

    protected $messages = [
        'name.required' => 'Sinf nomini kiritish majburiy',
        'name.min' => 'Sinf nomi kamida 2 ta belgidan iborat bo\'lishi kerak',
        'name.unique' => 'Bu sinf nomi allaqachon mavjud',
        'telegram_chat_id.required' => 'Telegram Chat ID ni kiritish majburiy',
        'telegram_chat_id.max' => 'Telegram Chat ID juda uzun',
        'telegram_topic_id.required' => 'Telegram Topic ID ni kiritish majburiy',
        'telegram_topic_id.max' => 'Telegram Topic ID juda uzun',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Save Class
    public function saveClass()
    {
        $this->validate();

        if ($this->isEdit) {
            $class = Classes::find($this->classId);
            $class->name = $this->name;
            $class->telegram_chat_id = $this->telegram_chat_id;
            $class->telegram_topic_id = $this->telegram_topic_id;
            $class->status = Classes::STATUS_ACTIVE;
            $class->save();

            session()->flash('message', 'Sinf muvaffaqiyatli yangilandi!');
        } else {
            Classes::create([
                'name' => $this->name,
                'telegram_chat_id' => $this->telegram_chat_id,
                'telegram_topic_id' => $this->telegram_topic_id,
                'status' => Classes::STATUS_ACTIVE,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            session()->flash('message', 'Yangi sinf qo\'shildi!');
        }

        $this->resetInputFields();
        $this->showModal = false;
    }

    // Edit Class
    public function editClass($id)
    {
        $class = Classes::findOrFail($id);
        $this->classId = $class->id;
        $this->name = $class->name;
        $this->telegram_chat_id = $class->telegram_chat_id;
        $this->telegram_topic_id = $class->telegram_topic_id;

        $this->isEdit = true;
        $this->showModal = true;
        $this->showViewModal = false;
    }

    // Delete Class
    public function deleteClass($id)
    {
        Classes::find($id)->delete();
        session()->flash('message', 'Sinf o\'chirildi!');
    }

    // Open Create Modal
    public function createClass()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    // Close Modal
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    // View Class with Students
    public function viewClass($id)
    {
        $this->viewingClass = Classes::findOrFail($id);

        // O'quvchilar sonini hisoblash
        $this->viewingClass->students_count_dynamic = Users::where('user_type', Users::TYPE_STUDENT)
            ->whereRaw('JSON_CONTAINS(classes_id, ?)', [json_encode((string) $id)])
            ->count();

        $this->studentsPage = 1;
        $this->showViewModal = true;
    }

    // Students Pagination
    public function nextStudentsPage()
    {
        $this->studentsPage++;
    }

    public function previousStudentsPage()
    {
        if ($this->studentsPage > 1) {
            $this->studentsPage--;
        }
    }

    // Get Students with Pagination
    public function getStudentsProperty()
    {
        if (!$this->viewingClass) {
            return collect();
        }

        return Users::where('user_type', Users::TYPE_STUDENT)
            ->whereRaw('JSON_CONTAINS(classes_id, ?)', [json_encode((string) $this->viewingClass->id)])
            ->orderBy('last_name')
            ->paginate($this->studentsPerPage, ['*'], 'studentsPage', $this->studentsPage);
    }

    // Close View Modal
    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingClass = null;
        $this->studentsPage = 1;
    }

    // Reset Input Fields
    private function resetInputFields()
    {
        $this->classId = null;
        $this->name = '';
        $this->telegram_chat_id = '';
        $this->telegram_topic_id = '';
        $this->isEdit = false;
    }

    public function render()
    {
        // Har bir sinf uchun o'quvchilar sonini hisoblash
        $classes = Classes::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name', 'asc')
            ->paginate(12);

        // Har bir sinfga o'quvchilar sonini qo'shish
        $classes->getCollection()->transform(function ($class) {
            $class->students_count = Users::where('user_type', Users::TYPE_STUDENT)
                ->whereRaw('JSON_CONTAINS(classes_id, ?)', [json_encode((string) $class->id)])
                ->count();
            return $class;
        });

        return view('livewire.backend.classes.classes-manager', [
            'classes' => $classes
        ]);
    }
}
