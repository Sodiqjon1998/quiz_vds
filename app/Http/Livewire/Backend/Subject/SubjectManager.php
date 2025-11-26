<?php

namespace App\Http\Livewire\Backend\Subject;

use App\Models\Subjects;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SubjectManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properties
    public $search = '';
    public $subjectId;
    public $name;
    public $status = 1; // Default active
    
    public $isEdit = false;
    public $showModal = false;

    // Validation rules
    protected function rules()
    {
        return [
            'name' => 'required|min:2|unique:subjects,name,' . $this->subjectId,
            'status' => 'required|boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'Fan nomini kiritish majburiy',
        'name.unique' => 'Bu fan allaqachon mavjud',
        'name.min' => 'Fan nomi kamida 2 harf bo\'lishi kerak',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // Search o'zgarganda sahifani reset qilish
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Modalni ochish (Yaratish uchun)
    public function createSubject()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    // Modalni ochish (Tahrirlash uchun)
    public function editSubject($id)
    {
        $subject = Subjects::findOrFail($id);
        $this->subjectId = $subject->id;
        $this->name = $subject->name;
        $this->status = $subject->status;
        
        $this->isEdit = true;
        $this->openModal();
    }

    // Saqlash (Create & Update)
    public function saveSubject()
    {
        $this->validate();

        // Hozirgi user ID sini olamiz
        $userId = Auth::id() ?? 1; // Agar auth bo'lmasa 1 (test uchun)

        if ($this->isEdit) {
            $subject = Subjects::find($this->subjectId);
            $subject->update([
                'name' => $this->name,
                'status' => $this->status,
                'updated_by' => $userId
            ]);
            session()->flash('message', 'Fan muvaffaqiyatli yangilandi!');
        } else {
            Subjects::create([
                'name' => $this->name,
                'status' => $this->status,
                'created_by' => $userId,
                'updated_by' => $userId
            ]);
            session()->flash('message', 'Yangi fan qo\'shildi!');
        }

        $this->closeModal();
        $this->resetInputFields();
    }

    // O'chirish
    public function deleteSubject($id)
    {
        Subjects::find($id)->delete();
        session()->flash('message', 'Fan o\'chirildi!');
    }

    // Modal funksiyalari
    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->status = 1;
        $this->subjectId = null;
        $this->isEdit = false;
    }

    public function render()
    {
        $subjects = Subjects::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.backend.subject.subject-manager', [
            'subjects' => $subjects
        ]);
    }
}