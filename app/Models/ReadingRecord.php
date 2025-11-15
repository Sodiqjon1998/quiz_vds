<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingRecord extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'reading_records';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'users_id',
        'filename',
        'file_url',
        'file_size',
        'duration',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'file_size' => 'integer',
        'duration' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * Get the user that owns the reading record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class);
    }

    /**
     * Scope: Faqat faol yozuvlar
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: Bugungi yozuvlar
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope: O'sha oydagi yozuvlar
     */
    public function scopeMonth($query, $month, $year)
    {
        return $query->whereMonth('created_at', $month)
                     ->whereYear('created_at', $year);
    }

    /**
     * Accessor: Fayl URL'ini qaytarish
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Accessor: Fayl hajmini human-readable formatda
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return round($bytes / (1024 * 1024), 2) . ' MB';
        }
    }

    /**
     * Accessor: Davomiylikni formatda qaytarish (HH:MM:SS)
     */
    public function getDurationFormattedAttribute(): string
    {
        $seconds = $this->duration;
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }
        
        return sprintf('%02d:%02d', $minutes, $secs);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Yozuv o'chirilganda faylni ham o'chirish
        static::deleting(function ($record) {
            if ($record->file_path && \Storage::disk('public')->exists($record->file_path)) {
                \Storage::disk('public')->delete($record->file_path);
            }
        });
    }
}