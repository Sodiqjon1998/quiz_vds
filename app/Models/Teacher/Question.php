<?php

namespace App\Models\Teacher;

use App\Models\ExamAnswer;
use App\Models\Option;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Option> $options
 * @property-read int|null $options_count
 * @property-read \App\Models\Teacher\Quiz $quiz
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
class Question extends \App\Models\Question
{
    use HasFactory;

    public static function find()
    {
        return static::query()->where('created_by', '=', \Auth::user()->id);
    }

    public static function getQuizList()
    {
        $quiz = Quiz::find()->get();
        return $quiz;
    }

    public static function getQuizById($id)
    {
        $quiz = Quiz::find()->findOrFail($id);
        return $quiz;
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    protected $fillable = [
        'quiz_id',
        'name',
        'image',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

    public function creator()
    {
        return $this->belongsTo(\App\Models\Users::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\Users::class, 'updated_by');
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    // Boot method for handling image deletion
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($question) {
            // Delete image when question is deleted
            if ($question->image) {
                \Storage::disk('public')->delete($question->image);
            }

            // Delete all related options
            $question->options()->delete();
        });
    }

    public function examAnswers()
    {
        return $this->hasMany(ExamAnswer::class, 'question_id');
    }
}
