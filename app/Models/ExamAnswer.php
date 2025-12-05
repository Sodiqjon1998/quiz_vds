<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $exam_id
 * @property int $question_id
 * @property int $option_id
 * @property int $created_by
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Question $question
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAnswer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAnswer whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAnswer whereOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAnswer whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class ExamAnswer extends Model
{
    use HasFactory;
    protected $table = 'exam_answer';
    public $timestamps = true;

    // App/Models/ExamAnswer.php
    protected $fillable = [
        'exam_id',
        'question_id',
        'option_id',
        'created_by',
        'updated_by',
        // Boshqa barcha mass assignment qilinadigan ustunlarni shu yerga qo'shing
    ];


    // Exam relation
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    // Question relation
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    // Option relation
    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id');
    }
}
