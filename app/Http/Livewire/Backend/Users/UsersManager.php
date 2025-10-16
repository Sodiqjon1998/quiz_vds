<?php

namespace App\Http\Livewire\Backend\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Modal
    public $showModal = false;
    public $showViewModal = false;
    public $isEdit = false;

    // Form fields
    public $userId;
    public $name;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $password;
    public $confirm_password;
    public $user_type = User::TYPE_ADMIN;
    public $status = User::STATUS_ACTIVE;
    public $subject_id;

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
                User::TYPE_ADMIN,
                User::TYPE_TEACHER,
                User::TYPE_KOORDINATOR,
            ])],
            'status' => ['required', Rule::in([
                User::STATUS_ACTIVE,
                User::STATUS_IN_ACTIVE,
            ])],
            'subject_id' => [
                Rule::requiredIf(fn() => $this->user_type === User::TYPE_TEACHER),
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
        if ($value !== User::TYPE_TEACHER) {
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
        $this->user_type = User::TYPE_ADMIN;
        $this->status = User::STATUS_ACTIVE;
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function editUser($id)
    {
        $this->resetValidation();
        $this->isEdit = true;
        $this->userId = $id;

        $user = User::findOrFail($id);

        $this->name = $user->name;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->user_type = $user->user_type;
        $this->status = $user->status;
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
            'subject_id' => $this->user_type === User::TYPE_TEACHER ? $this->subject_id : null,
        ];

        if ($this->password) {
            $data['password'] = Hash::make('12345678');
        }

        if ($this->isEdit) {
            User::findOrFail($this->userId)->update($data);
            session()->flash('message', 'Foydalanuvchi muvaffaqiyatli yangilandi!');
        } else {
            $data['password'] = Hash::make($this->password);
            User::create($data);
            session()->flash('message', 'Yangi foydalanuvchi qo\'shildi!');
        }

        $this->closeModal();
    }

    public function viewUser($id)
    {
        $this->viewingUser = User::findOrFail($id);
        $this->showViewModal = true;
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'Foydalanuvchi o\'chirildi!');
    }

    public function closeModal()
    {
        $this->showModal = false;
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
        $users = User::whereIn('user_type', [
            User::TYPE_ADMIN,
            User::TYPE_TEACHER,
            User::TYPE_KOORDINATOR,
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
    }
}
