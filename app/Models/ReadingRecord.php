<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReadingRecord extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    protected $fillable = [
        'users_id',
        "book_name",
        'file_url',
        'file_size',
        'filename',
        'duration',
        'status',
    ];

    protected $casts = [
        'file_size' => 'integer',
        "book_name" => 'string',
        'duration' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Foydalanuvchi bilan bog'lanish
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'users_id');
    }

    /**
     * Scope: Faqat aktiv yozuvlar
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: Ma'lum oy bo'yicha
     */
    public function scopeMonth($query, $month, $year)
    {
        return $query->whereYear('created_at', $year)
                     ->whereMonth('created_at', $month);
    }

    /**
     * Yozuv o'chirilganda faylni ham o'chirish
     */
    protected static function booted()
    {
        static::deleting(function ($record) {
            if ($record->file_url && Storage::disk('public')->exists($record->file_url)) {
                Storage::disk('public')->delete($record->file_url);
            }
        });
    }

    /**
     * File URL ni to'liq formatda qaytarish
     */
    public function getFileUrlAttribute($value)
    {
        if (!$value) return null;
        return Storage::disk('public')->url($value);
    }

    /**
     * File URL ni set qilish (faqat path)
     */
    public function setFileUrlAttribute($value)
    {
        $this->attributes['file_url'] = $value;
    }
}