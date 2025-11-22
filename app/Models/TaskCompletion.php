<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCompletion extends Model
{
    use HasFactory;

    protected $table = 'task_completions';

    protected $fillable = [
        'report_id',
        'task_name',
        'task_emoji',
        'is_completed',
    ];

    protected $casts = [
        // 'is_completed' => 'boolean',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Bir task completion bitta reportga tegishli
     */
    public function report()
    {
        return $this->belongsTo(DailyReport::class, 'report_id');
    }

    /**
     * Task statusini o'zgartirish
     */
    public function toggleStatus($status)
    {
        $this->is_completed = $status;
        $this->save();
        return $this;
    }

    /**
     * Scope: Faqat bajarilgan vazifalar
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope: Faqat bajarilmagan vazifalar
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Scope: Tanlanmagan vazifalar
     */
    public function scopeNotSelected($query)
    {
        return $query->whereNull('is_completed');
    }

    // Accessor qo'shamiz
    public function getIsCompletedAttribute($value)
    {
        if ($value === null) return null;
        return (bool) $value;
    }

    public function setIsCompletedAttribute($value)
    {
        if ($value === null) {
            $this->attributes['is_completed'] = null;
        } else {
            $this->attributes['is_completed'] = $value ? 1 : 0;
        }
    }
}
