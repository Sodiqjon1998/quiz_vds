<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $status
 * @property int $created_by
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Subjects newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subjects newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subjects query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subjects whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subjects whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subjects whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subjects whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subjects whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subjects whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subjects whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Subjects extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;


    use HasFactory;

    protected $table = 'subjects';


    public static function getStatus($id = null)
    {
        $status = [
            self::STATUS_ACTIVE => 'Faol',
            self::STATUS_IN_ACTIVE => "Bloklangan",
        ];

        return !is_null($id) ? $status[$id] : $status;
    }

    public static function getTeacherById($id)
    {
        $teacher = Users::where('subject_id', '=', $id)->get();
        return $teacher;
    }

    public static function getSubjectById($id){
        $model = Subjects::where('id', '=', $id)->first();
        return $model;
    }

    // public function
}
