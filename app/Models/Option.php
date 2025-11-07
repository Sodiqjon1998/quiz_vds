<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $question_id
 * @property string $name
 * @property int $is_correct
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Option newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Option newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Option query()
 * @method static \Illuminate\Database\Eloquent\Builder|Option whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Option whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Option whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Option whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Option whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Option whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Option whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Option whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Option whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Option extends Model
{
    use HasFactory;

    protected $table = 'option';

    public static function getOptionById($id)
    {
        $model = self::where('id', $id)->first();
        return $model;
    }


    protected $fillable = [
        'question_id',
        'name',
        'is_correct',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // Question relation
    public function question()
    {
        return $this->belongsTo(\App\Models\Teacher\Question::class);
    }
}
