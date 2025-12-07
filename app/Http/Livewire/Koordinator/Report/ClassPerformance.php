<?php

namespace App\Http\Livewire\Koordinator\Report;

use App\Models\Users;
use App\Models\Classes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Carbon\Carbon;

class ClassPerformance extends Component
{
    public $classFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $schoolName = "ANDIJAN RISING SCHOOL";
    public $className = '';

    // Tab holati: 'submitted' (topshirganlar) yoki 'missing' (topshirmaganlar)
    public $activeTab = 'submitted';

    public function mount($classId = null)
    {
        if ($classId) {
            $this->classFilter = $classId;
        }

        // Standart holatda faqat BUGUNGI KUN tanlanadi
        $this->dateFrom = Carbon::now()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');

        $this->updateClassName();
    }

    public function updatedClassFilter()
    {
        $this->updateClassName();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    private function updateClassName()
    {
        if ($this->classFilter) {
            $class = Classes::find($this->classFilter);
            if ($class) {
                $this->className = $class->name;
            }
        } else {
            $this->className = '';
        }
    }

    public function render()
    {
        if (!$this->classFilter) {
            return view('livewire.koordinator.report.class-performance', [
                'students' => collect([]),
                'topStudents' => [],
                'statistics' => null,
                'classes' => Classes::where('status', 1)->orderBy('name')->get(),
                'showEmptyMessage' => true,
            ]);
        }

        // Kesh kalitini o'zgartiramiz (versiya v4)
        $cacheKey = 'class_perf_v4_' . $this->classFilter . '_' . $this->dateFrom . '_' . $this->dateTo;

        $data = Cache::remember($cacheKey, 600, function () {
            // 1. Barcha o'quvchilar
            $students = Users::select(['id', 'first_name', 'last_name'])
                ->where('user_type', Users::TYPE_STUDENT)
                ->where('classes_id', $this->classFilter)
                ->where('status', 1)
                ->orderBy('first_name')
                ->get();

            $totalReports = 0;
            $totalTasks = 0;
            $completedTasksAll = 0;

            foreach ($students as $student) {
                // Hisobotlar
                $reports = DB::table('daily_reports')
                    ->where('student_id', $student->id)
                    ->whereBetween('report_date', [$this->dateFrom, $this->dateTo])
                    ->get();

                $student->total_reports = $reports->count();
                $totalReports += $student->total_reports;

                // Vazifalar
                $tasksQuery = DB::table('task_completions')
                    ->select(['task_completions.*', 'daily_reports.report_date'])
                    ->join('daily_reports', 'daily_reports.id', '=', 'task_completions.report_id')
                    ->where('daily_reports.student_id', $student->id)
                    ->whereBetween('daily_reports.report_date', [$this->dateFrom, $this->dateTo])
                    ->get();

                $student->total_tasks = $tasksQuery->count();
                $student->completed_tasks = $tasksQuery->where('is_completed', 1)->count();
                $student->task_completion_rate = $student->total_tasks > 0
                    ? round(($student->completed_tasks / $student->total_tasks) * 100)
                    : 0;

                // Vazifalar ro'yxati (Emojisiz)
                $student->tasks_list = $tasksQuery->map(function ($task) {
                    return [
                        'name' => $task->task_name,
                        'is_completed' => $task->is_completed,
                        'date' => $task->report_date,
                    ];
                });

                $totalTasks += $student->total_tasks;
                $completedTasksAll += $student->completed_tasks;

                // Test natijasi
                $examScore = DB::table('exam')
                    ->select([
                        DB::raw('SUM(CASE WHEN option.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                        DB::raw('COUNT(exam_answer.id) as total_questions')
                    ])
                    ->leftJoin('exam_answer', 'exam_answer.exam_id', '=', 'exam.id')
                    ->leftJoin('option', 'option.id', '=', 'exam_answer.option_id')
                    ->where('exam.user_id', $student->id)
                    ->whereBetween('exam.created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
                    ->first();

                $student->exam_score = ($examScore && $examScore->total_questions > 0)
                    ? round(($examScore->correct_answers / $examScore->total_questions) * 100)
                    : 0;

                // ✅ O'ZGARISH: Har bir bajarilgan vazifa uchun 1 ball
                $student->total_score_calc = ($student->total_reports * 10) + ($student->completed_tasks * 1) + $student->exam_score;
            }

            // Top o'quvchilar
            $sortedStudents = $students->sortByDesc('total_score_calc')->values();
            $topStudents = $sortedStudents->take(3)->map(function ($s) {
                return [
                    'name' => $s->first_name . ' ' . $s->last_name,
                    'reports_done' => $s->total_reports,
                    'tasks_done' => $s->completed_tasks,
                    'exam_score' => $s->exam_score,
                    'total_score' => $s->total_score_calc,
                ];
            });

            $statistics = [
                'total_reports' => $totalReports,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasksAll,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasksAll / $totalTasks) * 100) : 0,
                'students_count' => $students->count(),
            ];

            return [
                'students' => $students,
                'topStudents' => $topStudents,
                'statistics' => $statistics
            ];
        });

        // FILTRLASH (Tabga qarab)
        $filteredStudents = $data['students']->filter(function ($student) {
            if ($this->activeTab === 'submitted') {
                // Topshirganlar: Hisobot yoki vazifa soni 0 dan katta
                return $student->total_reports > 0 || $student->total_tasks > 0;
            } else {
                // Topshirmaganlar: Ikkisi ham 0
                return $student->total_reports == 0 && $student->total_tasks == 0;
            }
        })->values();

        $classes = Cache::remember('classes_list', 3600, fn() => Classes::where('status', 1)->orderBy('name')->get());

        return view('livewire.koordinator.report.class-performance', [
            'students' => $filteredStudents,
            'topStudents' => $data['topStudents'],
            'statistics' => $data['statistics'],
            'classes' => $classes,
            'showEmptyMessage' => false,
        ]);
    }
}
