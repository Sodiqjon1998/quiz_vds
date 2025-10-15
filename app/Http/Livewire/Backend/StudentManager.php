<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class StudentManager extends Component
{
    use WithPagination;

    // Properties
    public $search = '';
    public $studentId;
    public $name, $email, $phone, $password;
    public $isEdit = false;
    public $showModal = false;

    protected $paginationTheme = 'bootstrap';

    // Validation rules
    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->studentId,
            'phone' => 'nullable|string',
            'password' => $this->isEdit ? 'nullable|min:6' : 'required|min:6',
        ];
    }

    // Real-time validation
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // Qidiruv bo'yicha pagination reset
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Create/Update Student
    public function saveStudent()
    {
        $this->validate();

        if ($this->isEdit) {
            // Update
            $student = User::find($this->studentId);
            $student->name = $this->name;
            $student->email = $this->email;
            $student->phone = $this->phone;

            if ($this->password) {
                $student->password = bcrypt($this->password);
            }
            $student->save();

            session()->flash('message', 'Student muvaffaqiyatli yangilandi!');
        } else {
            // Create
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => bcrypt($this->password),
                'user_type' => User::TYPE_STUDENT
            ]);

            session()->flash('message', 'Yangi student qo\'shildi!');
        }

        $this->resetInputFields();
        $this->showModal = false;
    }

    // Edit Student
    public function editStudent($id)
    {
        $student = User::findOrFail($id);
        $this->studentId = $student->id;
        $this->name = $student->name;
        $this->email = $student->email;
        $this->phone = $student->phone;
        $this->isEdit = true;
        $this->showModal = true;
    }

    // Delete Student
    public function deleteStudent($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'Student o\'chirildi!');
    }

    // Open Create Modal
    public function createStudent()
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
    public $viewingStudent = null;

// View Student
    public function viewStudent($id)
    {
        $this->viewingStudent = User::findOrFail($id); // agar relation bo'lsa
        // yoki
        // $this->viewingStudent = User::findOrFail($id);
        $this->showViewModal = true;
    }

// Close View Modal
    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingStudent = null;
    }

    // Reset Input Fields
    private function resetInputFields()
    {
        $this->studentId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->isEdit = false;
    }

    // Render
    public function render()
    {
        $students = User::where('user_type', User::TYPE_STUDENT)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.backend.student-manager', [
            'students' => $students
        ]);
    }
}
