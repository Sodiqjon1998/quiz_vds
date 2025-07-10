<?php

namespace App\Models\Student;

use App\Models\User;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
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
        $koordinator = User::where('id', '=', $id)->first();
        return $koordinator->first_name . ' ' . $koordinator->last_name ?? "-----";
    }

    public static function getKordinatorList()
    {
        $koordinators = User::whereIn('user_type', [User::TYPE_KOORDINATOR])->get();
        return $koordinators;
    }


}
