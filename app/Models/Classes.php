<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property string|null $name
 * @property int $koordinator_id
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @method static \Illuminate\Database\Eloquent\Builder|Classes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Classes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Classes query()
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereKoordinatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Classes extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;

    use HasFactory;

    protected $table = "classes";

    public $timestamps = true;

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
        $koordinator = Users::where('id', '=', $id)->first();
        return $koordinator->last_name ?? "-----";
    }

    public static function getKordinatorList()
    {
        $koordinators = Users::whereIn('user_type', [Users::TYPE_KOORDINATOR])->get();
        return $koordinators;
    }


}
