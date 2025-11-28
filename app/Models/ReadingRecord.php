<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
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
            if ($record->file_url) {
                try {
                    // URL dan path ajratib olamiz
                    // Misol: https://s3.us-west-001.backblazeb2.com/.../readings/file.mp3
                    $parsedUrl = parse_url($record->file_url);
                    $path = ltrim($parsedUrl['path'], '/');

                    // Bucket nomini olib tashlash (agar URL da bo'lsa)
                    $bucketName = config('filesystems.disks.b2.bucket');
                    $path = str_replace($bucketName . '/', '', $path);

                    if (Storage::disk('b2')->exists($path)) {
                        Storage::disk('b2')->delete($path);
                        Log::info("File deleted from B2: " . $path);
                    }
                } catch (\Exception $e) {
                    Log::error('B2 file deletion failed: ' . $e->getMessage());
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
