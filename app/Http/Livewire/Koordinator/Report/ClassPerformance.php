<?php

namespace App\Http\Livewire\Koordinator\Report;

use App\Models\Users;
use App\Models\Classes;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;

class ClassPerformance extends Component
{
    public $classFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $schoolName = "ANDIJAN RISING SCHOOL";
    public $className = '';
    public $topStudents = [];

    public function mount($classId = null)
    {
        if ($classId) {
            $this->classFilter = $classId;
        }
        
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        
        $this->updateClassName();
    }

    public function updatedClassFilter()
    {
        $this->updateClassName();
        $this->calculateTopStudents();
    }

    public function updatedDateFrom()
    {
        $this->calculateTopStudents();
    }

    public function updatedDateTo()
    {
        $this->calculateTopStudents();
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

    private function calculateTopStudents()
    {
        if (!$this->classFilter) {
            $this->topStudents = [];
            return;
        }

        // ✅ Bir xil query ishlatamiz
        $students = Users::select(['id', 'first_name', 'last_name'])
            ->where('user_type', Users::TYPE_STUDENT)
            ->where('classes_id', $this->classFilter)
            ->where('status', 1)
            ->get();

        $studentScores = [];

        foreach ($students as $student) {
            $reportsDone = DB::table('daily_reports')
                ->where('student_id', $student->id)
                ->when($this->dateFrom, function($q) {
                    $q->where('report_date', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($q) {
                    $q->where('report_date', '<=', $this->dateTo);
                })
                ->count();

            $tasksDone = DB::table('task_completions')
                ->join('daily_reports', 'daily_reports.id', '=', 'task_completions.report_id')
                ->where('daily_reports.student_id', $student->id)
                ->where('task_completions.is_completed', 1)
                ->when($this->dateFrom, function($q) {
                    $q->where('daily_reports.report_date', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($q) {
                    $q->where('daily_reports.report_date', '<=', $this->dateTo);
                })
                ->count();

            $examScore = DB::table('exam')
                ->select([
                    DB::raw('SUM(CASE WHEN option.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                    DB::raw('COUNT(exam_answer.id) as total_questions')
                ])
                ->leftJoin('exam_answer', 'exam_answer.exam_id', '=', 'exam.id')
                ->leftJoin('option', 'option.id', '=', 'exam_answer.option_id')
                ->where('exam.user_id', $student->id)
                ->when($this->dateFrom, function($q) {
                    $q->where('exam.created_at', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($q) {
                    $q->where('exam.created_at', '<=', $this->dateTo . ' 23:59:59');
                })
                ->first();

            $examPercentage = $examScore && $examScore->total_questions > 0
                ? round(($examScore->correct_answers / $examScore->total_questions) * 100)
                : 0;

            $totalScore = ($reportsDone * 10) + ($tasksDone * 5) + $examPercentage;

            $studentScores[] = [
                'id' => $student->id,
                'name' => $student->first_name . ' ' . $student->last_name,
                'reports_done' => $reportsDone,
                'tasks_done' => $tasksDone,
                'exam_score' => $examPercentage,
                'total_score' => $totalScore,
            ];
        }

        usort($studentScores, function($a, $b) {
            return $b['total_score'] - $a['total_score'];
        });

        $this->topStudents = array_slice($studentScores, 0, 3);
    }

    public function render()
    {
        if (!$this->classFilter) {
            return view('livewire.koordinator.report.class-performance', [
                'students' => collect([]),
                'classes' => Classes::where('status', 1)->orderBy('name')->get(),
                'showEmptyMessage' => true,
                'statistics' => null,
            ]);
        }

        // ✅ To'g'ri query - join o'rniga where ishlatamiz
        $students = Users::select([
                'users.id',
                'users.first_name',
                'users.last_name',
            ])
            ->where('users.user_type', Users::TYPE_STUDENT)
            ->where('users.classes_id', $this->classFilter)
            ->where('users.status', 1)
            ->orderBy('users.first_name')
            ->get();

        // Har bir o'quvchi uchun ma'lumotlar
        foreach ($students as $student) {
            // Daily Reports
            $reports = DB::table('daily_reports')
                ->where('student_id', $student->id)
                ->when($this->dateFrom, function($q) {
                    $q->where('report_date', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($q) {
                    $q->where('report_date', '<=', $this->dateTo);
                })
                ->get();

            $student->total_reports = $reports->count();

            // Task Completions
            $tasks = DB::table('task_completions')
                ->select([
                    'task_completions.*',
                    'daily_reports.report_date',
                ])
                ->join('daily_reports', 'daily_reports.id', '=', 'task_completions.report_id')
                ->where('daily_reports.student_id', $student->id)
                ->when($this->dateFrom, function($q) {
                    $q->where('daily_reports.report_date', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function($q) {
                    $q->where('daily_reports.report_date', '<=', $this->dateTo);
                })
                ->get();

            $student->total_tasks = $tasks->count();
            $student->completed_tasks = $tasks->where('is_completed', 1)->count();
            $student->task_completion_rate = $student->total_tasks > 0 
                ? round(($student->completed_tasks / $student->total_tasks) * 100) 
                : 0;

            $student->tasks = $tasks->map(function($task) {
                return [
                    'name' => $task->task_name,
                    'emoji' => $task->task_emoji ?? '📝',
                    'is_completed' => $task->is_completed,
                    'date' => $task->report_date,
                ];
            });
        }

        // Umumiy statistika
        $totalReports = DB::table('daily_reports')
            ->join('users', 'users.id', '=', 'daily_reports.student_id')
            ->where('users.classes_id', $this->classFilter)
            ->where('users.status', 1)
            ->when($this->dateFrom, function($q) {
                $q->where('daily_reports.report_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($q) {
                $q->where('daily_reports.report_date', '<=', $this->dateTo);
            })
            ->count();

        $totalTasks = DB::table('task_completions')
            ->join('daily_reports', 'daily_reports.id', '=', 'task_completions.report_id')
            ->join('users', 'users.id', '=', 'daily_reports.student_id')
            ->where('users.classes_id', $this->classFilter)
            ->where('users.status', 1)
            ->when($this->dateFrom, function($q) {
                $q->where('daily_reports.report_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($q) {
                $q->where('daily_reports.report_date', '<=', $this->dateTo);
            })
            ->count();

        $completedTasks = DB::table('task_completions')
            ->join('daily_reports', 'daily_reports.id', '=', 'task_completions.report_id')
            ->join('users', 'users.id', '=', 'daily_reports.student_id')
            ->where('users.classes_id', $this->classFilter)
            ->where('users.status', 1)
            ->where('task_completions.is_completed', 1)
            ->when($this->dateFrom, function($q) {
                $q->where('daily_reports.report_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($q) {
                $q->where('daily_reports.report_date', '<=', $this->dateTo);
            })
            ->count();

        $statistics = [
            'total_reports' => $totalReports,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
            'students_count' => $students->count(),
        ];

        $this->calculateTopStudents();

        return view('livewire.koordinator.report.class-performance', [
            'students' => $students,
            'classes' => Classes::where('status', 1)->orderBy('name')->get(),
            'showEmptyMessage' => false,
            'statistics' => $statistics,
        ]);
    }
}