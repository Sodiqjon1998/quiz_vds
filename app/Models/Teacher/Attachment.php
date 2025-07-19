<?php

namespace App\Models\Teacher;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
class Attachment extends \App\Models\Attachment
{
    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;

    use HasFactory;

    public static function find()
    {
        return static::query()->where('created_by', '=', \Auth::user()->id);
    }

    public static function getQuizList()
    {
        $quizList = Quiz::where('status', '=', Quiz::STATUS_ACTIVE)->where('created_by', '=', Auth::user()->id)->get();
        return $quizList;
    }
}
