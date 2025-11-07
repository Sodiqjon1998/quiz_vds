<?php

namespace App\Models;

use App\Models\Teacher\Quiz;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string|null $img
 * @property int $quiz_id
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Option> $options
 * @property-read int|null $options_count
 * @method static \Illuminate\Database\Eloquent\Builder|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereQuizId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Question extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;


    use HasFactory;

    protected $table = 'question';

    public $timestamps = true;


    protected $fillable = [
        'quiz_id',
        'name',
        'status',
        'created_by',
        'updated_by',
    ];

    // Quiz relation
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Options relation
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    // Correct option
    public function correctOption()
    {
        return $this->hasOne(Option::class)->where('is_correct', 1);
    }

    // Creator
    public function creator()
    {
        return $this->belongsTo(\App\Models\Users::class, 'created_by');
    }


    public static function getStatus($id = null)
    {
        $status = [
            self::STATUS_ACTIVE => 'Faol',
            self::STATUS_IN_ACTIVE => "Bloklangan",
        ];

        return !is_null($id) ? $status[$id] : $status;
    }

    public static function getQuestionById($id)
    {
        $model = Question::where('id', $id)->first();
        return $model;
    }
}
