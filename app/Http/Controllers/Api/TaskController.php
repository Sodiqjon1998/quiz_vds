<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    private $allTasks = [
        ['id' => 1, 'name' => 'Erta uyg\'onish', 'emoji' => '🌅'],
        ['id' => 2, 'name' => 'Jismoniy tarbiya', 'emoji' => '🏃'],
        ['id' => 3, 'name' => 'Nonushtaga yordam', 'emoji' => '🍳'],
        ['id' => 4, 'name' => 'Xonani tartiblash', 'emoji' => '🏠'],
        ['id' => 5, 'name' => 'Duo qilish', 'emoji' => '🤲'],
        ['id' => 6, 'name' => 'Mehr berish', 'emoji' => '❤️'],
        ['id' => 7, 'name' => 'Kitob o\'qish', 'emoji' => '📚'],
        ['id' => 8, 'name' => 'Uy ishlariga yordam', 'emoji' => '🏠'],
        ['id' => 9, 'name' => '5 ta inglizcha so\'z', 'emoji' => '🔤'],
        ['id' => 10, 'name' => 'Oyoq kiyim tozalash', 'emoji' => '👟']
    ];

    public function getTasks(Request $request)
    {
        $userId = $request->user()->id;
        $date = $request->input('date', now()->toDateString());
        
        $report = DB::table('daily_reports')
            ->where('student_id', $userId)
            ->where('report_date', $date)
            ->first();

        $tasks = $this->allTasks;

        if ($report) {
            $completions = DB::table('task_completions')
                ->where('report_id', $report->id)
                ->get()
                ->keyBy('task_name');

            foreach ($tasks as &$task) {
                if (isset($completions[$task['name']])) {
                    $completed = $completions[$task['name']]->is_completed;
                    $task['is_completed'] = $completed == 1 ? true : ($completed == 0 ? false : null);
                } else {
                    $task['is_completed'] = null;
                }
            }
        } else {
            foreach ($tasks as &$task) {
                $task['is_completed'] = null;
            }
        }

        return response()->json([
            'success' => true,
            'data' => ['date' => $date, 'tasks' => $tasks]
        ]);
    }

    public function toggleTask(Request $request, $taskId)
    {
        $request->validate(['date' => 'required|date']);

        $userId = $request->user()->id;
        $date = $request->input('date');
        $isCompleted = $request->input('is_completed');

        $taskData = collect($this->allTasks)->firstWhere('id', (int)$taskId);

        if (!$taskData) {
            return response()->json(['success' => false, 'message' => 'Invalid task ID'], 400);
        }

        if ($isCompleted === true || $isCompleted === 'true' || $isCompleted === 1) {
            $value = 1;
        } elseif ($isCompleted === false || $isCompleted === 'false' || $isCompleted === 0) {
            $value = 0;
        } else {
            $value = null;
        }

        $report = DB::table('daily_reports')
            ->where('student_id', $userId)
            ->where('report_date', $date)
            ->first();

        if (!$report) {
            $reportId = DB::table('daily_reports')->insertGetId([
                'student_id' => $userId,
                'report_date' => $date,
                'created_at' => now()
            ]);
        } else {
            $reportId = $report->id;
        }

        $completion = DB::table('task_completions')
            ->where('report_id', $reportId)
            ->where('task_name', $taskData['name'])
            ->first();

        if ($completion) {
            DB::table('task_completions')->where('id', $completion->id)->update([
                'is_completed' => $value,
                'created_at' => now()
            ]);
        } else {
            DB::table('task_completions')->insert([
                'report_id' => $reportId,
                'task_name' => $taskData['name'],
                'task_emoji' => $taskData['emoji'],
                'is_completed' => $value,
                'created_at' => now()
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Task updated']);
    }

    public function monthlyStats(Request $request)
    {
        $userId = $request->user()->id;
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $reports = DB::table('daily_reports')
            ->where('student_id', $userId)
            ->whereYear('report_date', $year)
            ->whereMonth('report_date', $month)
            ->pluck('report_date')
            ->toArray();

        $stats = [];
        foreach ($reports as $date) {
            $stats[$date] = true;
        }

        return response()->json(['success' => true, 'data' => $stats]);
    }
}