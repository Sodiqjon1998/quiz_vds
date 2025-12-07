<?php

namespace App\Http\Livewire\Backend\Report;

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

    public $activeTab = 'submitted';

    public function mount($classId = null)
    {
        if ($classId) {
            $this->classFilter = $classId;
        }

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
            return view('livewire.backend.report.class-performance', [
                'students' => collect([]),
                'topStudents' => [],
                'statistics' => null,
                'classes' => Classes::where('status', 1)->orderBy('name')->get(),
                'showEmptyMessage' => true,
            ])
                ->extends('backend.layouts.main')
                ->section('content');
        }

        // Kesh kalitini yangilaymiz (v5)
        $cacheKey = 'admin_class_perf_v5_' . $this->classFilter . '_' . $this->dateFrom . '_' . $this->dateTo;

        $data = Cache::remember($cacheKey, 600, function () {
            // A. O'quvchilar
            $students = Users::select(['id', 'first_name', 'last_name'])
                ->where('user_type', Users::TYPE_STUDENT)
                ->where('classes_id', $this->classFilter)
                ->where('status', 1)
                ->orderBy('first_name')
                ->get();

            $totalReports = 0;
            $totalTasks = 0;
            $completedTasksAll = 0;

            // B. Hisob-kitob
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

                // ✅ TUZATILDI: 
                // Hisobot (Report) = 1 ball
                // Vazifa (Task) = 1 ball
                // Test (Exam) = foiz emas, to'g'ri javoblar soni yoki o'z holicha (agar foiz bo'lsa 100 gacha qo'shiladi)
                // Bu yerda exam_score foizda (0-100). Agar buni ham 1 ballga tenglashtirmoqchi bo'lsangiz, uni o'zgartirish kerak.
                // Hozircha: Report(1) + Task(1) + Exam(%)

                $student->total_score_calc = ($student->total_reports * 1) + ($student->completed_tasks * 1) + $student->exam_score;
            }

            // C. Saralash
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

            // D. Statistika
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

        // Filtrlash
        $filteredStudents = $data['students']->filter(function ($student) {
            if ($this->activeTab === 'submitted') {
                return $student->total_reports > 0 || $student->total_tasks > 0;
            } else {
                return $student->total_reports == 0 && $student->total_tasks == 0;
            }
        })->values();

        $classes = Cache::remember('admin_classes_list', 3600, fn() => Classes::where('status', 1)->orderBy('name')->get());

        return view('livewire.backend.report.class-performance', [
            'students' => $filteredStudents,
            'topStudents' => $data['topStudents'],
            'statistics' => $data['statistics'],
            'classes' => $classes,
            'showEmptyMessage' => false,
        ])
            ->extends('backend.layouts.main')
            ->section('content');
    }
}
