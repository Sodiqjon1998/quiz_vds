<?php

namespace App\Http\Livewire\Backend\Dashboard;

use App\Models\Users;
use App\Models\Classes;
use App\Models\Subjects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Carbon\Carbon;

class Statistics extends Component
{
    public $filterType = 'month';

    public function render()
    {
        // Kesh kaliti
        $cacheKey = 'admin_dashboard_stats_v6_' . $this->filterType;

        $stats = Cache::remember($cacheKey, 600, function () {
            // 1. ASOSIY RAQAMLAR
            $counts = [
                'students' => Users::where('user_type', Users::TYPE_STUDENT)->count(),
                'teachers' => Users::where('user_type', Users::TYPE_TEACHER)->count(),
                'classes' => Classes::count(),
                'subjects' => Subjects::count(),
            ];

            // 2. TEST VA KITOBXONLIK
            $examStats = DB::table('exam')->select(DB::raw('count(*) as total_exams'))->first();
            $examStats = $examStats ? (array) $examStats : ['total_exams' => 0];

            $readingStats = DB::table('reading_records')
                ->select([
                    DB::raw('count(*) as total_records'),
                    DB::raw('count(distinct users_id) as active_students')
                ])->first();
            $readingStats = $readingStats ? (array) $readingStats : ['total_records' => 0, 'active_students' => 0];

            // 3. TOP 5 SINF
            $topClasses = DB::table('classes')
                ->select('classes.name', DB::raw('count(daily_reports.id) as reports_count'))
                ->join('users', function ($join) {
                    $join->on(DB::raw('CAST(users.classes_id AS UNSIGNED)'), '=', 'classes.id');
                })
                ->join('daily_reports', 'daily_reports.student_id', '=', 'users.id')
                ->groupBy('classes.id', 'classes.name')
                ->orderByDesc('reports_count')
                ->limit(5)
                ->get();

            // 4. TOP 5 O'QITUVCHI
            $topTeachers = DB::table('users')
                ->select('users.first_name', 'users.last_name', DB::raw('count(quiz.id) as quiz_count'))
                ->join('quiz', 'quiz.created_by', '=', 'users.id')
                ->where('users.user_type', Users::TYPE_TEACHER)
                ->groupBy('users.id', 'users.first_name', 'users.last_name')
                ->orderByDesc('quiz_count')
                ->limit(5)
                ->get();

            // 5. HAFTALIK FAOLLIK GRAFIGI (Real ma'lumot)
            $chartActivity = [];
            $days = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $days[] = $date->format('d.m');

                $count = DB::table('daily_reports')
                    ->whereDate('report_date', $date->format('Y-m-d'))
                    ->count();

                $chartActivity[] = $count;
            }

            return [
                'counts' => $counts,
                'exam_total' => $examStats['total_exams'],
                'reading' => $readingStats,
                'top_classes' => $topClasses,
                'top_teachers' => $topTeachers,
                'chart_days' => $days,
                'chart_data' => $chartActivity
            ];
        });

        return view('livewire.backend.dashboard.statistics', [
            'stats' => $stats
        ]);
    }
}
