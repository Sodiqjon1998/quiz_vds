<?php

namespace App\Http\Livewire\Koordinator\Report;

use App\Models\Users;
use App\Models\Classes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
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
    public $selectedRecordId = null;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // Standart holatda BUGUNGI KUN
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
        $this->resetPage();
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

    // Modal uchun ma'lumot
    public function getSelectedRecordProperty()
    {
        if (!$this->selectedRecordId) return null;

        return DB::table('reading_records')
            ->select([
                'reading_records.*',
                'users.first_name',
                'users.last_name',
                'users.classes_id',
                'classes.name as class_name',
            ])
            ->leftJoin('users', 'users.id', '=', 'reading_records.users_id')
            // TUZATISH: classes_id ni to'g'ri formatlab ulash
            ->leftJoin('classes', function ($join) {
                $join->on(DB::raw('CAST(users.classes_id AS UNSIGNED)'), '=', 'classes.id');
            })
            ->where('reading_records.id', $this->selectedRecordId)
            ->first();
    }

    public function deleteRecord($recordId)
    {
        try {
            $record = DB::table('reading_records')->where('id', $recordId)->first();

            if ($record) {
                $filePath = storage_path('app/public/' . $record->file_url);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                DB::table('reading_records')->where('id', $recordId)->delete();
                session()->flash('message', 'Yozuv muvaffaqiyatli o\'chirildi!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $koordinatorClassIds = json_decode($user->classes_id, true) ?? [];

        // 1. Asosiy so'rov (Records)
        $records = DB::table('reading_records')
            ->select([
                'reading_records.*',
                'users.first_name',
                'users.last_name',
                'users.classes_id',
                'classes.name as class_name'
            ])
            ->leftJoin('users', 'users.id', '=', 'reading_records.users_id')
            // TUZATISH: classes_id ni to'g'ri formatlab ulash
            ->leftJoin('classes', function ($join) {
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
            ->paginate(15);

        // 2. Statistika (Kesh bilan)
        $cacheKey = 'reading_stats_' . md5(json_encode($koordinatorClassIds)) . '_' . $this->classFilter . '_' . $this->dateFrom . '_' . $this->dateTo;

        $statistics = Cache::remember($cacheKey, 300, function () use ($koordinatorClassIds) {
            $query = DB::table('reading_records')
                ->leftJoin('users', 'users.id', '=', 'reading_records.users_id');

            if (!empty($koordinatorClassIds)) {
                $query->whereIn('users.classes_id', $koordinatorClassIds);
            }
            if ($this->classFilter) {
                $query->where('users.classes_id', $this->classFilter);
            }

            $query->whereBetween('reading_records.created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59']);

            return [
                'total_records' => $query->count(),
                'total_students' => $query->distinct('reading_records.users_id')->count('reading_records.users_id'),
                'total_duration' => $this->formatDuration($query->sum('reading_records.duration')),
                'total_size' => $this->formatFileSize($query->sum('reading_records.file_size')),
            ];
        });

        // 3. Yordamchi ma'lumotlar (Kesh bilan)
        $classes = Cache::remember('reading_classes_list', 3600, function () use ($koordinatorClassIds) {
            $q = DB::table('classes')->where('status', 1);
            if (!empty($koordinatorClassIds)) $q->whereIn('id', $koordinatorClassIds);
            return $q->orderBy('name')->get();
        });

        // Studentlar ro'yxati filtrlash uchun
        $students = DB::table('users')
            ->select(['users.id', 'users.first_name', 'users.last_name', 'classes.name as class_name'])
            // TUZATISH: classes_id ni to'g'ri formatlab ulash
            ->leftJoin('classes', function ($join) {
                $join->on(DB::raw('CAST(users.classes_id AS UNSIGNED)'), '=', 'classes.id');
            })
            ->where('user_type', Users::TYPE_STUDENT)
            ->where('users.status', 1)
            ->when(!empty($koordinatorClassIds), function ($q) use ($koordinatorClassIds) {
                $q->whereIn('users.classes_id', $koordinatorClassIds);
            })
            ->when($this->classFilter, function ($q) {
                $q->where('users.classes_id', $this->classFilter);
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
        return $hours > 0 ? sprintf('%02d:%02d:%02d', $hours, $minutes, $secs) : sprintf('%02d:%02d', $minutes, $secs);
    }

    private function formatFileSize($bytes)
    {
        return number_format(($bytes / 1024 / 1024), 2) . ' Mb';
    }
}
