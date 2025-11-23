<?php

namespace App\Http\Livewire\Koordinator\Report;

use App\Models\Users;
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
        $user = Auth::user();

        // 1. Koordinator sinflarini olish
        $koordinatorClassIds = json_decode($user->classes_id, true) ?? [];

        // Agar bo'sh bo'lsa - BARCHA sinflar (Super Admin uchun)
        if (empty($koordinatorClassIds)) {
            $koordinatorClassIds = DB::table('classes')
                ->where('status', 1)
                ->pluck('id')
                ->toArray();
        }

        // 2. Bugun kitob tashlaganlar (faqat koordinator sinflari)
        $readersToday = DB::table('reading_records')
            ->join('users', 'users.id', '=', 'reading_records.users_id')
            ->whereDate('reading_records.created_at', $this->selectedDate)
            ->where('reading_records.status', 1)
            ->where('users.user_type', Users::TYPE_STUDENT)
            ->where('users.status', 1)
            ->whereIn('users.classes_id', $koordinatorClassIds) // ✅ Faqat o'z sinflari
            ->pluck('reading_records.users_id')
            ->unique()
            ->toArray();

        // 3. Barcha o'quvchilar (faqat koordinator sinflari)
        $allStudentsQuery = DB::table('users')
            ->where('user_type', Users::TYPE_STUDENT)
            ->where('status', 1)
            ->whereIn('classes_id', $koordinatorClassIds); // ✅ Faqat o'z sinflari

        // Agar sinf tanlangan bo'lsa
        if ($this->classFilter) {
            $allStudentsQuery->where('classes_id', $this->classFilter);
        }

        $allStudents = $allStudentsQuery->pluck('id')->toArray();

        // 4. Kitob tashlamaganlar
        $nonReadersIds = array_diff($allStudents, $readersToday);

        $nonReadersQuery = DB::table('users')
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.classes_id',
                'classes.name as class_name',
            ])
            ->leftJoin('classes', 'classes.id', '=', DB::raw('CAST(users.classes_id AS UNSIGNED)'))
            ->whereIn('users.id', $nonReadersIds)
            ->orderBy('classes.name')
            ->orderBy('users.first_name');

        $nonReaders = $nonReadersQuery->paginate(20);

        // 5. Sinflar ro'yxati (faqat koordinator sinflari)
        $classes = DB::table('classes')
            ->whereIn('id', $koordinatorClassIds) // ✅ Faqat o'z sinflari
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        // 6. Statistika
        $statistics = [
            'total_students' => count($allStudents),
            'read_today' => count($readersToday),
            'not_read_today' => count($nonReadersIds),
            'percentage' => count($allStudents) > 0 ? round((count($readersToday) / count($allStudents)) * 100, 1) : 0,
        ];

        return view('livewire.koordinator.report.non-readers-report', [
            'nonReaders' => $nonReaders,
            'classes' => $classes,
            'statistics' => $statistics,
        ]);
    }
}