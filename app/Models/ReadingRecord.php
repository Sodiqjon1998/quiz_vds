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
        'book_name',
        'file_url',      // To'liq URL (B2 yoki local)
        'file_path',     // B2 path (o'chirish uchun)
        'file_size',
        'filename',
        'duration',
        'status',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'book_name' => 'string',
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
     * Yozuv o'chirilganda faylni ham o'chirish (B2 yoki local)
     */
    protected static function booted()
    {
        static::deleting(function ($record) {
            // Agar file_path bor bo'lsa (B2 path), B2 dan o'chirish
            if ($record->file_path) {
                try {
                    Storage::disk('b2')->delete($record->file_path);
                    \Log::info('File deleted from B2: ' . $record->file_path);
                } catch (\Exception $e) {
                    \Log::error('B2 deletion failed: ' . $e->getMessage());
                }
            }
            // Agar eski local file bo'lsa
            elseif ($record->file_url && !str_starts_with($record->file_url, 'http')) {
                if (Storage::disk('public')->exists($record->file_url)) {
                    Storage::disk('public')->delete($record->file_url);
                    \Log::info('File deleted from local storage: ' . $record->file_url);
                }
            }
        });
    }

    /**
     * File URL ni to'liq formatda qaytarish
     * 
     * MUHIM: Agar file_url allaqachon to'liq URL bo'lsa (http...), 
     * o'shani qaytaradi. Aks holda local storage URL yasaydi.
     */
    public function getFileUrlAttribute($value)
    {
        if (!$value) return null;
        
        // Agar to'liq URL bo'lsa (B2 URL), o'shani qaytarish
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        
        // Agar nisbiy path bo'lsa, local storage URL yasash
        return Storage::disk('public')->url($value);
    }

    /**
     * File URL ni set qilish
     * To'liq URL yoki nisbiy path qabul qiladi
     */
    public function setFileUrlAttribute($value)
    {
        $this->attributes['file_url'] = $value;
    }
}