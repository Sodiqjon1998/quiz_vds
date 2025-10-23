<?php

namespace App\Http\Livewire\Backend\Users;

use App\Models\Users;
use Livewire\Component;
use Livewire\WithPagination;

class UsersManager extends Component
{


    use WithPagination;

    // Properties
    public $search = '';
    public $userId;
    public $name, $email, $phone, $password, $first_name, $last_name, $classes_id, $status;
    public $isEdit = false;
    public $showModal = false;

    protected $paginationTheme = 'bootstrap';

    // Validation rules
    protected function rules()
    {
        return [
            'name' => 'required|min:3|unique:users,name,' . $this->userId,
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'phone' => 'nullable|string',
            'password' => $this->isEdit ? 'nullable|min:6' : 'required|min:6',
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'classes_id' => 'required|exists:classes,id',
        ];
    }

    protected $messages = [
        'name.required' => 'Username kiritish majburiy',
        'name.unique' => 'Bu username allaqachon band',
        'first_name.required' => 'Ism kiritish majburiy',
        'last_name.required' => 'Familya kiritish majburiy',
        'email.required' => 'Email kiritish majburiy',
        'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan',
        'email.email' => 'Email noto\'g\'ri formatda',
        'password.required' => 'Parol kiritish majburiy',
        'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
        'classes_id.required' => 'Sinfni tanlash majburiy',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function saveUsers()
    {
        $this->validate();

        if ($this->isEdit) {
            // Update
            $users = Users::find($this->userId);
            $users->name = $this->name;
            $users->first_name = $this->first_name;
            $users->last_name = $this->last_name;
            $users->classes_id = $this->classes_id;
            $users->email = $this->email;
            $users->phone = $this->phone;
            $users->status = Users::STATUS_ACTIVE;

            if ($this->password) {
                $users->password = bcrypt($this->password);
            }
            $users->save();

            session()->flash('message', 'Student muvaffaqiyatli yangilandi!');
        } else {
            // Create
            Users::create([

                'name' => $this->name,
                'email' => $this->email,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'classes_id' => $this->classes_id,
                'status' => Users::STATUS_ACTIVE,
                'phone' => $this->phone,
                'password' => \Hash::make('12345678'),
                'user_type' => Users::TYPE_STUDENT
            ]);

            session()->flash('message', 'Yangi hodim qo\'shildi qo\'shildi!');
        }

        $this->resetInputFields();
        $this->showModal = false;
    }


    // Edit Student
    public function editUsers($id)
    {
        $users = Users::findOrFail($id);
        $this->userId = $users->id;
        $this->name = $users->name;
        $this->email = $users->email;
        $this->phone = $users->phone;
        $this->isEdit = true;
        $this->showModal = true;
    }

    // Delete Student
    public function deleteUsers($id)
    {
        Users::find($id)->delete();
        session()->flash('message', 'Hodim o\'chirildi!');
    }

    // Open Create Modal
    public function createUsers()
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
    public $viewingUsrs = null;

// View Usrs
    public function viewUsers($id)
    {
        $this->viewingUsrs = Users::findOrFail($id); // agar relation bo'lsa
        // yoki
        // $this->viewingStudent = Users::findOrFail($id);
        $this->showViewModal = true;
    }


    // Close View Modal
    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingUsrs = null;
    }

    // Reset Input Fields
    private function resetInputFields()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->isEdit = false;
    }

    public function render()
    {
        $users = Users::whereNotIn('user_type', [
            Users::TYPE_STUDENT,
            Users::TYPE_ADMIN
        ])->where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('livewire.backend.users.users-manager', [
            'users' => $users
        ]);
    }
}
