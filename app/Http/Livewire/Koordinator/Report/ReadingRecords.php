<?php

namespace App\Http\Livewire\Koordinator\Report;

use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ReadingRecords extends Component
{
    use WithPagination;

    public $search = '';
    public $classFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $studentFilter = '';

    // Modal
    public $showDetailModal = false;
    public $selectedRecord = null;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // Default sanalar (oxirgi 30 kun)
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingClassFilter()
    {
        $this->resetPage();
    }

    public function updatingStudentFilter()
    {
        $this->resetPage();
    }

    public function viewDetail($recordId)
    {
        $this->selectedRecord = DB::table('reading_records')
            ->select([
                'reading_records.*',
                'users.first_name',
                'users.last_name',
                'classes.name as class_name',
            ])
            ->join('users', 'users.id', '=', 'reading_records.users_id')
            ->leftJoin('classes', 'classes.id', '=', 'users.classes_id')
            ->where('reading_records.id', $recordId)
            ->first();

        if ($this->selectedRecord) {
            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedRecord = null;
    }

    public function deleteRecord($recordId)
    {
        try {
            $record = DB::table('reading_records')->where('id', $recordId)->first();

            if ($record) {
                // Faylni o'chirish
                $filePath = storage_path('app/public/' . $record->file_url);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Bazadan o'chirish
                DB::table('reading_records')->where('id', $recordId)->delete();

                session()->flash('message', 'Yozuv o\'chirildi!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // O'quvchilar va ularning yozuvlari
        $records = DB::table('reading_records')
            ->select([
                'reading_records.*',
                'users.first_name',
                'users.last_name',
                'users.classes_id',
                'classes.name as class_name',
            ])
            ->join('users', 'users.id', '=', 'reading_records.users_id')
            ->leftJoin('classes', 'classes.id', '=', 'users.classes_id')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('users.first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('users.last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('reading_records.filename', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->classFilter, function ($query) {
                $query->where('users.classes_id', $this->classFilter);
            })
            ->when($this->studentFilter, function ($query) {
                $query->where('reading_records.users_id', $this->studentFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->where('reading_records.created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->where('reading_records.created_at', '<=', $this->dateTo . ' 23:59:59');
            })
            ->orderBy('reading_records.created_at', 'desc')
            ->paginate(20);

        // Statistika
        $statistics = [
            'total_records' => DB::table('reading_records')->count(),
            'total_students' => DB::table('reading_records')->distinct('users_id')->count('users_id'),
            'total_duration' => $this->formatDuration(DB::table('reading_records')->sum('duration')),
            'total_size' => $this->formatFileSize(DB::table('reading_records')->sum('file_size')),
        ];

        // Sinflar ro'yxati
        $classes = DB::table('classes')->where('status', 1)->orderBy('name')->get();

        $students = DB::table('users')
            ->select(['users.id', 'users.first_name', 'users.last_name', 'users.classes_id', 'classes.name as class_name'])
            ->leftJoin('classes', 'classes.id', '=', 'users.classes_id')
            ->where('users.user_type', Users::TYPE_STUDENT) // ✅ STUDENT
            ->where('users.status', 1)
            ->when($this->classFilter, function ($query) {
                $query->where('users.classes_id', $this->classFilter);
            })
            ->orderBy('users.first_name')
            ->get();

        return view('livewire.koordinator.report.reading-records', [
            'records' => $records,
            'statistics' => $statistics,
            'classes' => $classes,
            'students' => $students,
        ]);
    }

    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }
        return sprintf('%02d:%02d', $minutes, $secs);
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
