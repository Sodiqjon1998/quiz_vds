<?php

namespace App\Http\Livewire\Koordinator\Report;

use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReadingRecords extends Component
{
    use WithPagination;

    public $search = '';
    public $classFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $studentFilter = '';

    // Modal - FIXED: Object emas, ID saqlaymiz
    public $showDetailModal = false;
    public $selectedRecordId = null;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // Default sanalar - BUGUNGI KUN
        $this->dateFrom = Carbon::now()->format('Y-m-d');
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

    // Tezkor sana tanlash
    public function setDateRange($range)
    {
        switch ($range) {
            case 'today':
                $this->dateFrom = Carbon::now()->format('Y-m-d');
                $this->dateTo = Carbon::now()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->dateFrom = Carbon::yesterday()->format('Y-m-d');
                $this->dateTo = Carbon::yesterday()->format('Y-m-d');
                break;
            case 'week':
                $this->dateFrom = Carbon::now()->subDays(6)->format('Y-m-d');
                $this->dateTo = Carbon::now()->format('Y-m-d');
                break;
            case 'month':
                $this->dateFrom = Carbon::now()->subDays(29)->format('Y-m-d');
                $this->dateTo = Carbon::now()->format('Y-m-d');
                break;
        }
    }

    public function viewDetail($recordId)
    {
        $this->selectedRecordId = $recordId;
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedRecordId = null;
    }

    // Getter method - view'da ishlatish uchun
    public function getSelectedRecordProperty()
    {
        if (!$this->selectedRecordId) {
            return null;
        }

        return DB::table('reading_records')
            ->select([
                'reading_records.*',
                'users.first_name',
                'users.last_name',
                'users.classes_id',
                'classes.name as class_name',
            ])
            ->join('users', 'users.id', '=', 'reading_records.users_id')
            ->leftJoin('classes', 'classes.id', '=', 'users.classes_id')
            ->where('reading_records.id', $this->selectedRecordId)
            ->first();
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
        // Koordinatorning JSON formatdagi classes_id
        $user = Auth::user();
        $koordinatorClassIds = json_decode($user->classes_id, true) ?? [];

        if (empty($koordinatorClassIds)) {
            $koordinatorClassIds = [];
        }

        // Faqat koordinator sinflaridagi o'quvchilarning yozuvlari
        $records = DB::table('reading_records')
            ->select([
                'reading_records.*',
                'users.first_name',
                'users.last_name',
                'users.classes_id',
                'classes.name as class_name',
                DB::raw('CAST(users.classes_id AS CHAR) as classes_id_raw') // DEBUG
            ])
            ->join('users', 'users.id', '=', 'reading_records.users_id')
            ->leftJoin('classes', function($join) {
                // INT yoki STRING bo'lsa ham ishlaydi
                $join->on(DB::raw('CAST(users.classes_id AS UNSIGNED)'), '=', 'classes.id');
            })
            ->when(!empty($koordinatorClassIds), function ($query) use ($koordinatorClassIds) {
                $query->whereIn('users.classes_id', $koordinatorClassIds);
            })
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
        $statisticsQuery = DB::table('reading_records')
            ->join('users', 'users.id', '=', 'reading_records.users_id');

        if (!empty($koordinatorClassIds)) {
            $statisticsQuery->whereIn('users.classes_id', $koordinatorClassIds);
        }

        $statistics = [
            'total_records' => (clone $statisticsQuery)->count(),
            'total_students' => (clone $statisticsQuery)->distinct('reading_records.users_id')->count('reading_records.users_id'),
            'total_duration' => $this->formatDuration((clone $statisticsQuery)->sum('reading_records.duration')),
            'total_size' => $this->formatFileSize((clone $statisticsQuery)->sum('reading_records.file_size')),
        ];

        // Sinflar ro'yxati - koordinator sinflari
        $classes = DB::table('classes')
            ->whereIn('id', $koordinatorClassIds)
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        // O'quvchilar - faqat koordinator sinflaridan
        $students = DB::table('users')
            ->select([
                'users.id', 
                'users.first_name', 
                'users.last_name', 
                'users.classes_id',
                'classes.name as class_name'
            ])
            ->leftJoin('classes', 'classes.id', '=', 'users.classes_id')
            ->where('user_type', Users::TYPE_STUDENT)
            ->where('users.status', 1)
            ->when(!empty($koordinatorClassIds), function ($query) use ($koordinatorClassIds) {
                $query->whereIn('users.classes_id', $koordinatorClassIds);
            })
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
        // Megabit (Mb) ga o'tkazish
        $kilobytes = $bytes / 1024;
        $kilobits = $kilobytes * 8;
        $megabits = $kilobits / 1000;
        
        return number_format($megabits, 2) . ' Mb';
    }
}