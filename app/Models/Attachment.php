<?php

namespace App\Models;

use App\Models\Teacher\Quiz;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $quiz_id
 * @property string|null $date
 * @property string|null $time
 * @property int $number
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereQuizId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Attachment extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;

    use HasFactory;

    protected $table = 'attachment';

    public $timestamps = true;

    public static function getQuizId($id)
    {
        $quiz = Quiz::findOrFail($id);

        return $quiz;
    }


    public static function getStatus($id = null)
    {
        $status = [
            self::STATUS_ACTIVE => 'Faol',
            self::STATUS_IN_ACTIVE => "Bloklangan",
        ];

        return !is_null($id) ? $status[$id] : $status;
    }

    public static function getAttamptById($id = null){
        $model = self::where('quiz_id', $id)->first();
        return $model;
    }
}
