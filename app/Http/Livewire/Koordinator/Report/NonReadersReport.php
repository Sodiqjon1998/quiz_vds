<?php

namespace App\Http\Livewire\Koordinator\Report;

use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

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
        $koordinatorClassIds = json_decode($user->classes_id, true) ?? [];

        // Kesh kaliti
        $cacheKey = 'non_readers_v2_' . md5(json_encode($koordinatorClassIds)) . '_' . $this->selectedDate . '_' . $this->classFilter;

        // Ma'lumotlarni keshdan olish yoki hisoblash (10 daqiqa)
        $data = Cache::remember($cacheKey, 600, function () use ($koordinatorClassIds) {

            // 1. Agar koordinator sinflari bo'lmasa, barcha sinflarni olamiz
            if (empty($koordinatorClassIds)) {
                $koordinatorClassIds = DB::table('classes')->where('status', 1)->pluck('id')->toArray();
            }

            // 2. Bugun kitob tashlaganlar (faqat kerakli sinflar)
            $readersToday = DB::table('reading_records')
                ->join('users', 'users.id', '=', 'reading_records.users_id')
                ->whereDate('reading_records.created_at', $this->selectedDate)
                ->where('reading_records.status', 1)
                ->where('users.user_type', Users::TYPE_STUDENT)
                ->where('users.status', 1)
                ->whereIn('users.classes_id', $koordinatorClassIds)
                ->pluck('reading_records.users_id')
                ->unique()
                ->toArray();

            // 3. Barcha o'quvchilar IDsi
            $allStudentsQuery = DB::table('users')
                ->where('user_type', Users::TYPE_STUDENT)
                ->where('status', 1)
                ->whereIn('classes_id', $koordinatorClassIds);

            if ($this->classFilter) {
                $allStudentsQuery->where('classes_id', $this->classFilter);
            }

            $allStudentsIds = $allStudentsQuery->pluck('id')->toArray();

            // 4. Kitob tashlamaganlar IDsi
            $nonReadersIds = array_diff($allStudentsIds, $readersToday);

            // 5. Statistika
            $statistics = [
                'total_students' => count($allStudentsIds),
                'read_today' => count($readersToday),
                'not_read_today' => count($nonReadersIds),
                'percentage' => count($allStudentsIds) > 0
                    ? round(((count($allStudentsIds) - count($nonReadersIds)) / count($allStudentsIds)) * 100, 1)
                    : 0,
            ];

            return [
                'nonReadersIds' => $nonReadersIds,
                'statistics' => $statistics,
                'classIds' => $koordinatorClassIds
            ];
        });

        // 6. Kitob tashlamaganlar ro'yxatini olish (Paginate keshlanmaydi)
        // ✅ TUZATISH: Classes join qismida CAST ishlatildi
        $nonReaders = DB::table('users')
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.classes_id',
                'classes.name as class_name',
            ])
            ->leftJoin('classes', function ($join) {
                $join->on(DB::raw('CAST(users.classes_id AS UNSIGNED)'), '=', 'classes.id');
            })
            ->whereIn('users.id', $data['nonReadersIds'])
            ->orderBy('classes.name')
            ->orderBy('users.first_name')
            ->paginate(20);

        // 7. Sinflar ro'yxati (Kesh - 1 soat)
        $classes = Cache::remember('classes_list_non_readers', 3600, function () use ($data) {
            return DB::table('classes')
                ->whereIn('id', $data['classIds'])
                ->where('status', 1)
                ->orderBy('name')
                ->get();
        });

        return view('livewire.koordinator.report.non-readers-report', [
            'nonReaders' => $nonReaders,
            'classes' => $classes,
            'statistics' => $data['statistics'],
        ]);
    }
}
