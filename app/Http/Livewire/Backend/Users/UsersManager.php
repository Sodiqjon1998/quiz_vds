<?php

<<<<<<< HEAD
namespace App\Http\Livewire\Backend\Users;

use Illuminate\Foundation\Auth\User;
=======
namespace App\Livewire\Backend\Users;

>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;
<<<<<<< HEAD
=======
use Illuminate\Validation\Rule;
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6

class UsersManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

<<<<<<< HEAD
=======
    // Modal
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
    public $showModal = false;
    public $showViewModal = false;
    public $isEdit = false;

<<<<<<< HEAD
    // User properties
    public $user_id;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $status = Users::STATUS_ACTIVE;
    public $role = Users::TYPE_TEACHER;

    // Search
    public $search = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'role' => 'required',
    ];

    public function render()
    {
        $users = Users::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.backend.users.users-manager', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
        $this->isEdit = false;
    }

    public function store()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        session()->flash('message', 'User created successfully!');
        $this->resetInputFields();
        $this->showModal = false;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'role' => 'required',
        ]);

        $user = User::find($this->user_id);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ]);

        if ($this->password) {
            $user->update([
                'password' => Hash::make($this->password)
            ]);
        }

        session()->flash('message', 'User updated successfully!');
        $this->resetInputFields();
        $this->showModal = false;
    }

    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'User deleted successfully!');
    }

    public function view($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;

        $this->showViewModal = true;
    }

    private function resetInputFields()
    {
        $this->user_id = '';
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'user';
=======
    // Form fields
    public $userId;
    public $name;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $password;
    public $confirm_password;
    public $user_type;
    public $status;
    public $subject_id;

    // Constructor - default qiymatlar
    public function mount()
    {
        $this->user_type = $this->user_type ?? Users::TYPE_ADMIN;
        $this->status = $this->status ?? Users::STATUS_ACTIVE;
    }

    // Table
    public $search = '';
    public $viewingUser;

    protected function rules()
    {
        return [
            'name' => ['required', 'min:3', 'max:50', Rule::unique('users')->ignore($this->userId)],
            'first_name' => ['required', 'min:2', 'max:50'],
            'last_name' => ['required', 'min:2', 'max:50'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => $this->isEdit ? ['nullable', 'min:6', 'max:50'] : ['required', 'min:6', 'max:50'],
            'confirm_password' => $this->isEdit ? ['nullable', 'same:password'] : ['required', 'same:password'],
            'user_type' => ['required', Rule::in([
                Users::TYPE_ADMIN,
                Users::TYPE_TEACHER,
                Users::TYPE_KOORDINATOR,
            ])],
            'status' => ['required', Rule::in([
                Users::STATUS_ACTIVE,
                Users::STATUS_IN_ACTIVE,
            ])],
            'subject_id' => [
                Rule::requiredIf(fn() => $this->user_type === Users::TYPE_TEACHER),
                'nullable',
                'exists:subjects,id'
            ],
        ];
    }

    protected $messages = [
        'name.required' => 'Login kiritish majburiy',
        'name.unique' => 'Bu login allaqachon band',
        'first_name.required' => 'Ism kiritish majburiy',
        'last_name.required' => 'Familya kiritish majburiy',
        'email.required' => 'Email kiritish majburiy',
        'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan',
        'email.email' => 'Email noto\'g\'ri formatda',
        'password.required' => 'Parol kiritish majburiy',
        'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
        'confirm_password.same' => 'Parollar mos kelmadi',
        'user_type.required' => 'Lavozimni tanlang',
        'status.required' => 'Statusni tanlang',
        'subject_id.required' => 'O\'qituvchi uchun fanni tanlash majburiy',
    ];

    // User type o'zgarganda
    public function updatedUserType($value)
    {
        if ($value !== Users::TYPE_TEACHER) {
            $this->subject_id = null;
        }
    }

    public function createUser()
    {
        $this->resetValidation();
        $this->reset([
            'userId', 'name', 'first_name', 'last_name', 'email',
            'phone', 'password', 'confirm_password', 'subject_id'
        ]);
        $this->user_type = Users::TYPE_ADMIN;
        $this->status = Users::STATUS_ACTIVE;
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function editUser($id)
    {
        $this->resetValidation();
        $this->isEdit = true;
        $this->userId = $id;

        $user = Users::findOrFail($id);

        $this->name = $user->name;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->user_type = $user->user_type;
        $this->status = (int) $user->status; // Integer ga aylantirish
        $this->subject_id = $user->subject_id;
        $this->password = '';
        $this->confirm_password = '';

        $this->showModal = true;
    }

    public function saveUser()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'user_type' => $this->user_type,
            'status' => $this->status,
            'subject_id' => $this->user_type === Users::TYPE_TEACHER ? $this->subject_id : null,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEdit) {
            Users::findOrFail($this->userId)->update($data);
            session()->flash('message', 'Foydalanuvchi muvaffaqiyatli yangilandi!');
        } else {
            $data['password'] = Hash::make($this->password);
            Users::create($data);
            session()->flash('message', 'Yangi foydalanuvchi qo\'shildi!');
        }

        $this->closeModal();
    }

    public function viewUser($id)
    {
        $this->viewingUser = Users::with('subject')->findOrFail($id);
        $this->showViewModal = true;
    }

    public function deleteUser($id)
    {
        Users::findOrFail($id)->delete();
        session()->flash('message', 'Foydalanuvchi o\'chirildi!');
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
    }

    public function closeModal()
    {
        $this->showModal = false;
<<<<<<< HEAD
        $this->showViewModal = false;
        $this->resetInputFields();
=======
        $this->reset([
            'userId', 'name', 'first_name', 'last_name', 'email',
            'phone', 'password', 'confirm_password', 'user_type',
            'status', 'subject_id', 'isEdit'
        ]);
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingUser = null;
    }

    public function render()
    {
        $users = Users::whereIn('user_type', [
//            Users::TYPE_ADMIN,
            Users::TYPE_TEACHER,
            Users::TYPE_KOORDINATOR,
        ])
            ->when($this->search, fn($query) =>
            $query->where('first_name', 'like', '%' . $this->search . '%')
                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                ->orWhere('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
            )
            ->latest()
            ->paginate(25);

        return view('livewire.backend.users.users-manager', compact('users'));
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
    }
}
