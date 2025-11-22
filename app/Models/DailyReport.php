<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $table = 'daily_reports';

    protected $fillable = [
        'student_id',
        'report_date',
    ];

    protected $casts = [
        'report_date' => 'date',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Bir report bitta studentga tegishli
     */
    public function student()
    {
        return $this->belongsTo(Users::class, 'student_id');
    }

    /**
     * Bir reportda ko'plab vazifalar bo'ladi
     */
    public function taskCompletions()
    {
        return $this->hasMany(TaskCompletion::class, 'report_id');
    }

    /**
     * Reportni to'liq ma'lumot bilan olish
     */
    public function getFullReport()
    {
        return [
            'id' => $this->id,
            'student' => $this->student->name,
            'date' => $this->report_date->format('Y-m-d'),
            'tasks' => $this->taskCompletions->map(function($task) {
                return [
                    'name' => $task->task_name,
                    'emoji' => $task->task_emoji,
                    'is_completed' => $task->is_completed
                ];
            })
        ];
    }
}