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
        ];
    }

    protected $messages = [
        'name.required' => 'Sinf nomini kiritish majburiy',
        'name.min' => 'Sinf nomi kamida 2 ta belgidan iborat bo\'lishi kerak',
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

    // Save Class
    public function saveClass()
    {
        $this->validate();

        if ($this->isEdit) {
            $class = Classes::find($this->classId);
            $class->name = $this->name;
            $class->status = Classes::STATUS_ACTIVE;
            $class->save();

            session()->flash('message', 'Sinf muvaffaqiyatli yangilandi!');
        } else {
            Classes::create([
                'name' => $this->name,
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
        $this->isEdit = true;
        $this->showModal = true;
        $this->showViewModal = false; // Close view modal if open
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
        $this->viewingClass = Classes::withCount('students')->findOrFail($id);
        $this->studentsPage = 1; // Reset pagination
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
    // Get Students with Pagination
    public function getStudentsProperty()
    {
        if (!$this->viewingClass) {
            return collect();
        }

        // ✅ TO'G'RI: classes_id ni string sifatida solishtirish
        return Users::where('classes_id', (string) $this->viewingClass->id)
            ->where('user_type', Users::TYPE_STUDENT)
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
        $this->isEdit = false;
    }

    public function render()
    {
        $classes = Classes::withCount('students')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name', 'asc')
            ->paginate(12);

        return view('livewire.backend.classes.classes-manager', [
            'classes' => $classes
        ]);
    }
}
