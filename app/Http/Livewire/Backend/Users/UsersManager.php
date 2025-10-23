<?php

namespace App\Http\Livewire\Backend\Users;

use Illuminate\Foundation\Auth\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;

class UsersManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $showModal = false;
    public $showViewModal = false;
    public $isEdit = false;

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
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showViewModal = false;
        $this->resetInputFields();
    }
}
