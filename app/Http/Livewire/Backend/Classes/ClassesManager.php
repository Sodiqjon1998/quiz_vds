<?php

namespace App\Http\Livewire\Backend\Classes;

use App\Models\Classes;
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
            // Update
            $class = Classes::find($this->classId);
            $class->name = $this->name;
            $class->status = Classes::STATUS_ACTIVE;
            $class->save();

            session()->flash('message', 'Sinf muvaffaqiyatli yangilandi!');
        } else {
            // Create
            Classes::create([
                'name' => $this->name,
                'status' => Classes::STATUS_ACTIVE,
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

    public $showViewModal = false;
    public $viewingClass = null;

    // View Class
    public function viewClass($id)
    {
        $this->viewingClass = Classes::findOrFail($id);
        $this->showViewModal = true;
    }

    // Close View Modal
    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingClass = null;
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
        $classes = Classes::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.backend.classes.classes-manager', [
            'classes' => $classes
        ]);
    }
}
