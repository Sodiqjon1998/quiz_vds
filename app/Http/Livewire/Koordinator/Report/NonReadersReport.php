<?php

namespace App\Http\Livewire\Koordinator\Report;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NonReadersReport extends Component
{
    use WithPagination;

    public $selectedDate;
    public $classFilter = '';

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
    }

    public function updated($propertyName)
    {
        $this->resetPage();
    }

    public function setDate($range)
    {
        switch ($range) {
            case 'today':
                $this->selectedDate = Carbon::now()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->selectedDate = Carbon::yesterday()->format('Y-m-d');
                break;
        }
        $this->resetPage();
    }

    public function render()
    {
        // Koordinatorning sinflari
        $user = Auth::user();
        $koordinatorClassIds = json_decode($user->classes_id, true) ?? [];

        if (empty($koordinatorClassIds)) {
            $koordinatorClassIds = DB::table('classes')
                ->where('status', 1)
                ->pluck('id')
                ->toArray();
        }

        // 1. TANLANGAN KUNDA KITOB TASHLAGAN O'QUVCHILAR
        $readersQuery = DB::table('reading_records')
            ->join('users', 'users.id', '=', 'reading_records.users_id')
            ->whereDate('reading_records.created_at', $this->selectedDate)
            ->where('reading_records.status', 1)
            ->where('users.user_type', 3)
            ->where('users.status', 1);

        // ✅ whereIn ishlatamiz
        if (!empty($koordinatorClassIds)) {
            $readersQuery->whereIn('users.classes_id', $koordinatorClassIds);
        }

        $readersToday = $readersQuery
            ->pluck('reading_records.users_id')
            ->unique()
            ->toArray();

        // 2. JAMI O'QUVCHILAR
        $allStudentsQuery = DB::table('users')
            ->where('user_type', 3)
            ->where('status', 1);

        if (!empty($koordinatorClassIds)) {
            $allStudentsQuery->whereIn('classes_id', $koordinatorClassIds);
        }

        if ($this->classFilter) {
            $allStudentsQuery->where('classes_id', $this->classFilter);
        }

        $totalStudents = $allStudentsQuery->count();

        // 3. KITOB TASHLAMAGAN O'QUVCHILAR
        $nonReadersQuery = DB::table('users')
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.classes_id',
                'classes.name as class_name',
            ])
            ->leftJoin('classes', 'classes.id', '=', DB::raw('CAST(users.classes_id AS UNSIGNED)'))
            ->where('users.user_type', 3)
            ->where('users.status', 1);

        if (!empty($koordinatorClassIds)) {
            $nonReadersQuery->whereIn('users.classes_id', $koordinatorClassIds);
        }

        if ($this->classFilter) {
            $nonReadersQuery->where('users.classes_id', $this->classFilter);
        }

        // ✅ whereNotIn to'g'ri ishlatamiz
        if (!empty($readersToday)) {
            $nonReadersQuery->whereNotIn('users.id', $readersToday);
        }

        $nonReaders = $nonReadersQuery
            ->orderBy('classes.name')
            ->orderBy('users.first_name')
            ->paginate(20);

        // 4. SINFLAR
        $classes = DB::table('classes')
            ->whereIn('id', $koordinatorClassIds)
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        // 5. STATISTIKA
        $readCount = count($readersToday);
        $notReadCount = $totalStudents - $readCount;

        $statistics = [
            'total_students' => $totalStudents,
            'read_today' => $readCount,
            'not_read_today' => $notReadCount,
            'percentage' => $totalStudents > 0 ? round(($readCount / $totalStudents) * 100, 1) : 0,
        ];

        return view('livewire.koordinator.report.non-readers-report', [
            'nonReaders' => $nonReaders,
            'classes' => $classes,
            'statistics' => $statistics,
        ]);
    }
}