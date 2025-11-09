<?php

namespace App\Models\Teacher;

use App\Models\Users;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $quiz_id
 * @property string $date
 * @property string $time
 * @property int|null $number
 * @property int $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Quiz $quiz
 */
class Attachment extends Model
{
    protected $table = 'attachment';

    public $timestamps = true;

    protected $fillable = [
        'quiz_id',
        'date',
        'time',
        'number',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quiz_id' => 'integer',
        'number' => 'integer',
        'status' => 'integer',
        'date' => 'date',
    ];

    // ======================
    // === RELATIONSHIPS ===
    // ======================

    /**
     * Attachment quiz bilan bog'lanish
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    /**
     * Yaratuvchi foydalanuvchi
     */
    public function creator()
    {
        return $this->belongsTo(Users::class, 'created_by');
    }

    /**
     * Yangilovchi foydalanuvchi
     */
    public function updater()
    {
        return $this->belongsTo(Users::class, 'updated_by');
    }

    // ======================
    // === ACCESSORS ===
    // ======================

    /**
     * Vaqtni formatlangan holda olish
     * Masalan: "00:30:00" -> "30 daqiqa"
     */
    public function getFormattedTimeAttribute()
    {
        if (!$this->time) return null;

        $parts = explode(':', $this->time);
        $hours = (int)$parts[0];
        $minutes = (int)$parts[1];
        $seconds = (int)$parts[2] ?? 0;

        $result = [];
        if ($hours > 0) $result[] = $hours . ' soat';
        if ($minutes > 0) $result[] = $minutes . ' daqiqa';
        if ($seconds > 0) $result[] = $seconds . ' soniya';

        return implode(' ', $result) ?: '0 soniya';
    }

    /**
     * Sanani formatlangan holda olish
     */
    public function getFormattedDateAttribute()
    {
        return $this->date ? \Carbon\Carbon::parse($this->date)->format('d.m.Y') : null;
    }

    // ======================
    // === SCOPES ===
    // ======================

    /**
     * Faqat faol attachmentlar
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Muddati o'tmagan attachmentlar
     */
    public function scopeNotExpired($query)
    {
        return $query->whereDate('date', '>=', now()->toDateString());
    }

    /**
     * Bugungi attachmentlar
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }

    /**
     * Muddati o'tgan attachmentlar
     */
    public function scopeExpired($query)
    {
        return $query->whereDate('date', '<', now()->toDateString());
    }
}
