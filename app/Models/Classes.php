<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;

    use HasFactory;

    protected $table = "classes";

    public $timestamps = false;

    protected $fillable = [
        'name',
        'telegram_chat_id',
        'telegram_topic_id',
        'status',
        'created_by',
        'updated_by',
    ];

    public static function getStatus($id = null)
    {
        $status = [
            self::STATUS_ACTIVE => 'Faol',
            self::STATUS_IN_ACTIVE => "Bloklangan",
        ];

        return !is_null($id) ? $status[$id] : $status;
    }

    public static function getKoordinator($id)
    {
        $koordinator = Users::where('id', '=', $id)->first();
        return $koordinator->last_name ?? "-----";
    }

    public static function getKordinatorList()
    {
        $koordinators = Users::whereIn('user_type', [Users::TYPE_KOORDINATOR])->get();
        return $koordinators;
    }

    // ✅ JSON formatdagi classes_id uchun o'quvchilar sonini hisoblash
    public function getStudentsCountAttribute()
    {
        return Users::where('user_type', Users::TYPE_STUDENT)
            ->whereRaw('JSON_CONTAINS(classes_id, ?)', [json_encode((string) $this->id)])
            ->count();
    }
}
