<?php

namespace App\Http\Livewire\Teacher\Classes;

use App\Models\Classes;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ClassesManager extends Component
{
    use WithPagination;

    // Properties
    public $search = '';
    public $classId;
    public $name;
    public $status = 1;

    public $isEdit = false;
    public $showModal = false;
    public $showViewModal = false;
    public $viewingClass = null;

    public $studentsPage = 1;
    public $studentsPerPage = 5;

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'name' => 'required|min:2|unique:classes,name,' . $this->classId,
            'status' => 'required|in:0,1',
        ];
    }

    protected $messages = [
        'name.required' => 'Sinf nomini kiritish majburiy',
        'name.unique' => 'Bu sinf nomi allaqachon mavjud',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function saveClass()
    {
        $this->validate();

        if ($this->isEdit) {
            $class = Classes::find($this->classId);
            $class->update([
                'name' => $this->name,
                'status' => $this->status,
                'updated_by' => Auth::id(),
            ]);
            session()->flash('message', 'Sinf yangilandi!');
        } else {
            Classes::create([
                'name' => $this->name,
                'status' => $this->status,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            session()->flash('message', 'Yangi sinf qo\'shildi!');
        }
        $this->closeModal();
    }

    public function editClass($id)
    {
        $class = Classes::findOrFail($id);
        $this->classId = $class->id;
        $this->name = $class->name;
        $this->status = $class->status;
        $this->isEdit = true;
        $this->showModal = true;
        $this->showViewModal = false;
    }

    public function deleteClass($id)
    {
        Classes::find($id)->delete();
        session()->flash('message', 'Sinf o\'chirildi!');
    }

    public function createClass()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    // --- VIEW MODAL LOGIKASI ---

    public function viewClass($id)
    {
        $this->viewingClass = Classes::findOrFail($id);
        
        // O'quvchilar sonini hisoblash - LIKE operatori
        $count = Users::where('user_type', Users::TYPE_STUDENT)
            ->where('status', 1)
            ->where(function($q) use ($id) {
                $q->where('classes_id', 'LIKE', '%"' . $id . '"%')
                  ->orWhere('classes_id', 'LIKE', '%' . $id . ',%')
                  ->orWhere('classes_id', 'LIKE', '%,' . $id . '%')
                  ->orWhere('classes_id', 'LIKE', '%[' . $id . ']%')
                  ->orWhere('classes_id', 'LIKE', '%[' . $id . ',%');
            })
            ->count();
            
        $this->viewingClass->students_count_dynamic = $count;

        $this->studentsPage = 1;
        $this->showViewModal = true;
    }

    // O'quvchilar ro'yxatini olish
    public function getStudentsProperty()
    {
        if (!$this->viewingClass) {
            return collect();
        }

        return Users::query()
            ->where('user_type', Users::TYPE_STUDENT)
            ->where('status', 1)
            ->where(function($query) {
                // LIKE operatori
                $query->where('classes_id', 'LIKE', '%"' . $this->viewingClass->id . '"%')
                      ->orWhere('classes_id', 'LIKE', '%' . $this->viewingClass->id . ',%')
                      ->orWhere('classes_id', 'LIKE', '%,' . $this->viewingClass->id . '%')
                      ->orWhere('classes_id', 'LIKE', '%[' . $this->viewingClass->id . ']%')
                      ->orWhere('classes_id', 'LIKE', '%[' . $this->viewingClass->id . ',%');
            })
            ->orderBy('last_name')
            ->paginate($this->studentsPerPage, ['*'], 'studentsPage', $this->studentsPage);
    }

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

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingClass = null;
        $this->studentsPage = 1;
    }

    private function resetInputFields()
    {
        $this->classId = null;
        $this->name = '';
        $this->status = 1;
        $this->isEdit = false;
        $this->resetValidation();
    }

    // --- ASOSIY RENDER - TO'G'RILANDI ---

    public function render()
    {
        // Har bir sinf uchun o'quvchilar sonini hisoblash
        $classes = Classes::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name', 'asc')
            ->paginate(12);

        // Har bir sinf uchun o'quvchilar sonini qo'shish
        foreach ($classes as $class) {
            $count = Users::where('user_type', Users::TYPE_STUDENT)
                ->where('status', 1)
                ->where(function($q) use ($class) {
                    // LIKE operatori - text tipidagi JSON array uchun
                    $q->where('classes_id', 'LIKE', '%"' . $class->id . '"%')
                      ->orWhere('classes_id', 'LIKE', '%' . $class->id . ',%')
                      ->orWhere('classes_id', 'LIKE', '%,' . $class->id . '%')
                      ->orWhere('classes_id', 'LIKE', '%[' . $class->id . ']%')
                      ->orWhere('classes_id', 'LIKE', '%[' . $class->id . ',%');
                })
                ->count();
            
            $class->students_count = $count;
        }

        return view('livewire.teacher.classes.classes-manager', [
            'classes' => $classes,
        ]);
    }
}