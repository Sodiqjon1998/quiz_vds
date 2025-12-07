<?php

namespace App\Http\Livewire\Backend\Dashboard;

use App\Models\Users;
use App\Models\Classes;
use App\Models\Subjects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Statistics extends Component
{
    // Vaqt oralig'i (Default: Oy)
    public $filterType = 'month';

    public function render()
    {
        // Kesh kaliti (Har safar kod o'zgarganda versiyani yangilaymiz: v3)
        $cacheKey = 'admin_dashboard_stats_v3_' . $this->filterType;

        $stats = Cache::remember($cacheKey, 600, function () {
            // 1. ASOSIY RAQAMLAR
            $counts = [
                'students' => Users::where('user_type', Users::TYPE_STUDENT)->count(),
                'teachers' => Users::where('user_type', Users::TYPE_TEACHER)->count(),
                'classes' => Classes::count(),
                'subjects' => Subjects::count(),
            ];

            // 2. TEST NATIJALARI
            $examStats = DB::table('exam')
                ->select(DB::raw('count(*) as total_exams'))
                ->first();

            $examStats = $examStats ? (array) $examStats : ['total_exams' => 0];

            // 3. KITOBXONLIK
            $readingStats = DB::table('reading_records')
                ->select([
                    DB::raw('count(*) as total_records'),
                    DB::raw('count(distinct users_id) as active_students')
                ])
                ->first();

            $readingStats = $readingStats ? (array) $readingStats : ['total_records' => 0, 'active_students' => 0];

            // 4. TOP 5 FAOL SINFLAR (TUZATILDI)
            // Muammo shu yerda edi: classes_id ni to'g'ri formatlab ulash kerak
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

            // 5. ENG FAOL O'QITUVCHILAR
            $topTeachers = DB::table('users')
                ->select('users.first_name', 'users.last_name', DB::raw('count(quiz.id) as quiz_count'))
                ->join('quiz', 'quiz.created_by', '=', 'users.id')
                ->where('users.user_type', Users::TYPE_TEACHER)
                ->groupBy('users.id', 'users.first_name', 'users.last_name')
                ->orderByDesc('quiz_count')
                ->limit(5)
                ->get();

            return [
                'counts' => $counts,
                'exam_total' => $examStats['total_exams'],
                'reading' => $readingStats,
                'top_classes' => $topClasses,
                'top_teachers' => $topTeachers
            ];
        });

        return view('livewire.backend.dashboard.statistics', [
            'stats' => $stats
        ]);
    }
}
