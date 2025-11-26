<?php

namespace App\Http\Livewire\Backend\Users;

use App\Models\Classes;
use App\Models\Users;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class UsersManager extends Component
{
    use WithPagination;

    // Properties
    public $search = '';
    public $userId;
    public $name, $email, $phone, $password, $first_name, $last_name, $subject_id, $status, $user_type;
    public $classes_id = []; // Ko'p sinflar uchun array
    public $isEdit = false;
    public $showModal = false;

    protected $paginationTheme = 'bootstrap';

    // Validation rules
    protected function rules()
    {
        $rules = [
            'name' => 'required|min:3|unique:users,name,' . $this->userId,
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'phone' => 'nullable|string',
//            'password' => $this->isEdit ? 'nullable|min:6' : 'required|min:6',
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'user_type' => 'required|in:' . Users::TYPE_TEACHER . ',' . Users::TYPE_KOORDINATOR,
        ];

        // Faqat o'qituvchi uchun fan majburiy
        if ($this->user_type == Users::TYPE_TEACHER) {
            $rules['subject_id'] = 'required|exists:subjects,id';
        } else {
            $rules['subject_id'] = 'nullable|exists:subjects,id';
        }

        // Faqat koordinator uchun sinflar majburiy
        if ($this->user_type == Users::TYPE_KOORDINATOR) {
            $rules['classes_id'] = 'required|array|min:1';
            $rules['classes_id.*'] = 'exists:classes,id';
        } else {
            $rules['classes_id'] = 'nullable|array';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Username kiritish majburiy',
        'name.unique' => 'Bu username allaqachon band',
        'first_name.required' => 'Ism kiritish majburiy',
        'last_name.required' => 'Familya kiritish majburiy',
        'email.required' => 'Email kiritish majburiy',
        'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan',
        'email.email' => 'Email noto\'g\'ri formatda',
        'subject_id.required' => 'Fanni tanlash majburiy',
        'user_type.required' => 'Hodim turini tanlash majburiy',
        'classes_id.required' => 'Kamida bitta sinf tanlash majburiy',
        'classes_id.min' => 'Kamida bitta sinf tanlang',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        // Ism va familya o'zgarganda username generatsiya qilish
        if (in_array($propertyName, ['first_name', 'last_name']) && !$this->isEdit) {
            $this->generateUsername();
        }

        // O'qituvchi tanlansa, sinflarni tozalash
        if ($propertyName === 'user_type' && $this->user_type == Users::TYPE_TEACHER) {
            $this->classes_id = [];
        }

        // Koordinator tanlansa, fanni tozalash
        if ($propertyName === 'user_type' && $this->user_type == Users::TYPE_KOORDINATOR) {
            $this->subject_id = null;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Username generatsiya qilish
    public function generateUsername()
    {
        if (!empty($this->first_name) && !empty($this->last_name)) {
            $firstName = $this->transliterate(strtolower($this->first_name));
            $lastName = $this->transliterate(strtolower($this->last_name));
            $randomNum = rand(100, 999);

            $baseUsername = $firstName . $lastName . $randomNum;

            // Agar username band bo'lsa, yangi raqam qo'shish
            $username = $baseUsername;
            $counter = 1;
            while (Users::where('name', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $this->name = $username;
        }
    }

    // Kirill harflarini lotin harflariga o'girish
    private function transliterate($text)
    {
        $cyrillic = [
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п',
            'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
            'ў', 'қ', 'ғ', 'ҳ', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л',
            'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь',
            'Э', 'Ю', 'Я', 'Ў', 'Қ', 'Ғ', 'Ҳ'
        ];

        $latin = [
            'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p',
            'r', 's', 't', 'u', 'f', 'x', 'ts', 'ch', 'sh', 'sh', '', 'i', '', 'e', 'yu', 'ya',
            'o', 'q', 'g', 'h', 'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l',
            'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'x', 'ts', 'ch', 'sh', 'sh', '', 'i', '',
            'e', 'yu', 'ya', 'o', 'q', 'g', 'h'
        ];

        return str_replace($cyrillic, $latin, $text);
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
            $users->email = $this->email;
            $users->phone = $this->phone;
            $users->user_type = $this->user_type;
            $users->status = Users::STATUS_ACTIVE;

            // Hodim turiga qarab fan yoki sinflar saqlash
            if ($this->user_type == Users::TYPE_TEACHER) {
                $users->subject_id = $this->subject_id;
                $users->classes_id = null;
            } elseif ($this->user_type == Users::TYPE_KOORDINATOR) {
                $users->subject_id = null;
                $users->classes_id = json_encode($this->classes_id);
            }

            if ($this->password) {
                $users->password = bcrypt($this->password);
            }
            $users->save();

            session()->flash('message', 'Hodim muvaffaqiyatli yangilandi!');
        } else {
            // Create
            Users::create([
                'name' => $this->name,
                'email' => $this->email,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'subject_id' => $this->user_type == Users::TYPE_TEACHER ? $this->subject_id : null,
                'classes_id' => $this->user_type == Users::TYPE_KOORDINATOR ? json_encode($this->classes_id) : null,
                'status' => Users::STATUS_ACTIVE,
                'phone' => $this->phone,
                'password' => \Hash::make('12345678'),
                'user_type' => $this->user_type
            ]);

            session()->flash('message', 'Yangi hodim qo\'shildi!');
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
        $this->first_name = $users->first_name;
        $this->last_name = $users->last_name;
        $this->subject_id = $users->subject_id;
        $this->user_type = $users->user_type;
        $this->isEdit = true;
        $this->showModal = true;

        // Agar classes_id JSON/array bo'lsa va modelda cast qilingan bo'lsa:
        $this->classes_id = is_array($users->classes_id) ? $users->classes_id : (
        ($users->classes_id && $users->user_type == Users::TYPE_KOORDINATOR) ? json_decode($users->classes_id, true) : []
        );

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

    // View Users
    public function viewUsers($id)
    {
        $this->viewingUsrs = Users::findOrFail($id);
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
        $this->first_name = '';
        $this->last_name = '';
        $this->subject_id = '';
        $this->user_type = '';
        $this->classes_id = [];
        $this->isEdit = false;
    }

    public function render()
    {
        $query = Users::with(['subject'])
            ->whereNotIn('user_type', [
                Users::TYPE_STUDENT,
                Users::TYPE_ADMIN
            ]);

        // Qidiruv logikasi
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $term = '%' . $this->search . '%';
                
                // 1. Ism, Familiya, Username, Email, Telefon bo'yicha qidiruv
                $q->where('name', 'like', $term)
                  ->orWhere('first_name', 'like', $term)
                  ->orWhere('last_name', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('phone', 'like', $term);

                // 2. Fan nomi bo'yicha qidiruv (Relation)
                $q->orWhereHas('subject', function ($subQ) use ($term) {
                    $subQ->where('name', 'like', $term);
                });

                // 3. Sinf nomi bo'yicha qidiruv (JSON ichidan)
                // Avval qidirilayotgan so'zga mos sinf IDlarini topamiz
                $matchingClassIds = Classes::where('name', 'like', $term)->pluck('id')->toArray();
                
                if (!empty($matchingClassIds)) {
                    foreach ($matchingClassIds as $id) {
                        // JSON array ichida shu ID bormi tekshiramiz
                        // Eslatma: Agar bazada IDlar string saqlangan bo'lsa (string)$id, integer bo'lsa $id
                        $q->orWhereJsonContains('classes_id', (string)$id);
                        $q->orWhereJsonContains('classes_id', (int)$id); 
                    }
                }
            });
        }

        $users = $query->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('livewire.backend.users.users-manager', [
            'users' => $users
        ]);
    }
}
