<?php

namespace App\Http\Livewire\Backend\Student;

use App\Models\Users;
use App\Models\Classes;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;

class StudentManager extends Component
{
    use WithPagination;

    // Properties
    public $search = '';
    public $classFilter = '';
    public $studentId;
    public $name, $email, $phone, $first_name, $last_name, $classes_id, $status;
    public $isEdit = false;
    public $showModal = false;

    // View Modal
    public $showViewModal = false;
    public $viewingStudent = null;

    protected $paginationTheme = 'bootstrap';

    // Validation rules
    protected function rules()
    {
        return [
            'name' => 'required|min:3|unique:users,name,' . $this->studentId,
            'email' => 'required|email|unique:users,email,' . $this->studentId,
            'phone' => 'nullable|string',
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
        'classes_id.required' => 'Sinfni tanlash majburiy',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        // Yangi student uchun username generatsiya
        if (in_array($propertyName, ['first_name', 'last_name']) && !$this->isEdit) {
            $this->generateUsername();
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Sinf filtri o'zgarganda sahifani reset qilish
    public function updatingClassFilter()
    {
        $this->resetPage();
    }

    public function generateUsername()
    {
        if (!empty($this->first_name) && !empty($this->last_name)) {
            $firstName = $this->transliterate(strtolower($this->first_name));
            $lastName = $this->transliterate(strtolower($this->last_name));
            $randomNum = rand(100, 999);

            $baseUsername = $firstName . $lastName . $randomNum;

            $username = $baseUsername;
            $counter = 1;
            while (Users::where('name', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $this->name = $username;
        }
    }

    private function transliterate($text)
    {
        $cyrillic = [
            'а',
            'б',
            'в',
            'г',
            'д',
            'е',
            'ё',
            'ж',
            'з',
            'и',
            'й',
            'к',
            'л',
            'м',
            'н',
            'о',
            'п',
            'р',
            'с',
            'т',
            'у',
            'ф',
            'х',
            'ц',
            'ч',
            'ш',
            'щ',
            'ъ',
            'ы',
            'ь',
            'э',
            'ю',
            'я',
            'ў',
            'қ',
            'ғ',
            'ҳ',
            'А',
            'Б',
            'В',
            'Г',
            'Д',
            'Е',
            'Ё',
            'Ж',
            'З',
            'И',
            'Й',
            'К',
            'Л',
            'М',
            'Н',
            'О',
            'П',
            'Р',
            'С',
            'Т',
            'У',
            'Ф',
            'Х',
            'Ц',
            'Ч',
            'Ш',
            'Щ',
            'Ъ',
            'Ы',
            'Ь',
            'Э',
            'Ю',
            'Я',
            'Ў',
            'Қ',
            'Ғ',
            'Ҳ'
        ];

        $latin = [
            'a',
            'b',
            'v',
            'g',
            'd',
            'e',
            'yo',
            'zh',
            'z',
            'i',
            'y',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'r',
            's',
            't',
            'u',
            'f',
            'x',
            'ts',
            'ch',
            'sh',
            'sh',
            '',
            'i',
            '',
            'e',
            'yu',
            'ya',
            'o',
            'q',
            'g',
            'h',
            'a',
            'b',
            'v',
            'g',
            'd',
            'e',
            'yo',
            'zh',
            'z',
            'i',
            'y',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'r',
            's',
            't',
            'u',
            'f',
            'x',
            'ts',
            'ch',
            'sh',
            'sh',
            '',
            'i',
            '',
            'e',
            'yu',
            'ya',
            'o',
            'q',
            'g',
            'h'
        ];

        return str_replace($cyrillic, $latin, $text);
    }

    public function saveStudent()
    {
        $this->validate();

        if ($this->isEdit) {
            $student = Users::find($this->studentId);
            $student->update([
                'name' => $this->name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                // classes_id is saved as integer ID
                'classes_id' => $this->classes_id,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => Users::STATUS_ACTIVE,
            ]);
            session()->flash('message', 'Student yangilandi!');
        } else {
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
            session()->flash('message', 'Yangi student qo\'shildi!');
        }

        $this->resetInputFields();
        $this->showModal = false;
    }

    // ... (editStudent, deleteStudent, createStudent, closeModal, viewStudent, closeViewModal, resetInputFields metodlari)

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

    public function deleteStudent($id)
    {
        Users::find($id)->delete();
        session()->flash('message', 'Student o\'chirildi!');
    }

    public function createStudent()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function viewStudent($id)
    {
        $this->viewingStudent = Users::with('classRelation')->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingStudent = null;
    }

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

    /**
     * Joriy filtrlar bo'yicha o'quvchilar ro'yxatini username va parol bilan export qilish.
     */
    public function exportCredentialsToExcel()
    {
        $term = '%' . $this->search . '%';

        // Barcha filtrlangan o'quvchilarni olish uchun so'rovni yaratish
        $studentsQuery = Users::with('classRelation')
            ->where('user_type', Users::TYPE_STUDENT)
            ->where(function ($query) use ($term) {
                // Qidiruv shartlari
                $query->where('name', 'like', $term)
                    ->orWhere('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhereHas('classRelation', function ($q) use ($term) {
                        $q->where('name', 'like', $term);
                    });
            });

        // Sinf bo'yicha filtrlash
        if ($this->classFilter) {
            $studentsQuery->where('classes_id', $this->classFilter);
        }

        // Barcha mos keluvchi studentlarni olish (paginationsiz)
        $studentsToExport = $studentsQuery->orderBy('first_name', 'asc')->get();

        if ($studentsToExport->isEmpty()) {
            session()->flash('error', 'Eksport qilish uchun hech qanday o\'quvchi topilmadi.');
            return;
        }

        $fileName = 'Student_Credentials_' . date('Ymd_His') . '.xlsx';

        // Excel faylini yuklab olish
        return Excel::download(new StudentsExport($studentsToExport), $fileName);
    }

    public function render()
    {
        $term = '%' . $this->search . '%';

        $studentsQuery = Users::with('classRelation')
            ->where('user_type', Users::TYPE_STUDENT)
            ->where(function ($query) use ($term) {
                // Ism, Familiya, Username, Email, Telefon bo'yicha
                $query->where('name', 'like', $term)
                    ->orWhere('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    // Sinf nomi bo'yicha qidiruv (Relation orqali)
                    ->orWhereHas('classRelation', function ($q) use ($term) {
                        $q->where('name', 'like', $term);
                    });
            });

        if ($this->classFilter) {
            $studentsQuery->where('classes_id', $this->classFilter);
        }

        $students = $studentsQuery->orderBy('created_at', 'desc')->paginate(10);

        $classes = Classes::where('status', 1)->orderBy('name')->get();

        return view('livewire.backend.student.student-manager', [
            'students' => $students,
            'classes' => $classes,
        ]);
    }
}
