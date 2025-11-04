<?php

namespace App\Http\Livewire\Backend\Student;

use App\Models\Users;
use Livewire\Component;
use Livewire\WithPagination;

class StudentManager extends Component
{
    use WithPagination;

    // Properties
    public $search = '';
    public $studentId;
    public $name, $email, $phone, $first_name, $last_name, $classes_id, $status;
    public $isEdit = false;
    public $showModal = false;

    protected $paginationTheme = 'bootstrap';

    // Validation rules
    protected function rules()
    {
        $rules = [
            'name' => 'required|min:3|unique:users,name,' . $this->studentId,
            'email' => 'required|email|unique:users,email,' . $this->studentId,
            'phone' => 'nullable|string',
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'classes_id' => 'required|exists:classes,id',
        ];

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
        'classes_id.required' => 'Sinfni tanlash majburiy',
    ];

    // Real-time validation
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        // Ism va familya o'zgarganda username generatsiya qilish (faqat yangi student uchun)
        if (in_array($propertyName, ['first_name', 'last_name']) && !$this->isEdit) {
            $this->generateUsername();
        }
    }

    // Qidiruv bo'yicha pagination reset
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Username avtomatik generatsiya qilish
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

    // Create/Update Student
    public function saveStudent()
    {
        $this->validate();

        if ($this->isEdit) {
            // Update
            $student = Users::find($this->studentId);
            $student->name = $this->name;
            $student->first_name = $this->first_name;
            $student->last_name = $this->last_name;
            $student->classes_id = $this->classes_id;
            $student->email = $this->email;
            $student->phone = $this->phone;
            $student->status = Users::STATUS_ACTIVE;
            $student->save();

            session()->flash('message', 'Student muvaffaqiyatli yangilandi!');
        } else {
            // Create (parol avtomatik 12345678)
            Users::create([
                'name' => $this->name,
                'email' => $this->email,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'classes_id' => $this->classes_id,
                'status' => Users::STATUS_ACTIVE,
                'phone' => $this->phone,
                'password' => \Hash::make('12345678'), // Default parol
                'user_type' => Users::TYPE_STUDENT
            ]);

            session()->flash('message', 'Yangi student qo\'shildi! (Parol: 12345678)');
        }

        $this->resetInputFields();
        $this->showModal = false;
    }

    // Edit Student
    public function editStudent($id)
    {
        $student = Users::findOrFail($id);
        $this->studentId = $student->id;
        $this->name = $student->name;
        $this->first_name = $student->first_name;
        $this->last_name = $student->last_name;
        $this->classes_id = $student->classes_id;
        $this->email = $student->email;
        $this->phone = $student->phone;
        $this->isEdit = true;
        $this->showModal = true;
    }

    // Delete Student
    public function deleteStudent($id)
    {
        Users::find($id)->delete();
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
        $this->viewingStudent = Users::with('classRelation')->findOrFail($id);
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
        $this->first_name = '';
        $this->last_name = '';
        $this->classes_id = null;
        $this->isEdit = false;
    }

    // Render
    public function render()
    {
        $students = Users::with('classRelation')
            ->where('user_type', Users::TYPE_STUDENT)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.backend.student.student-manager', [
            'students' => $students
        ]);
    }
}