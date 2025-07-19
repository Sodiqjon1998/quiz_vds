<?php

namespace App\Models;

use App\Models\Teacher\Quiz;
use App\Models\Teacher\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $subject_id
 * @property int $quiz_id
 * @property int $user_id
 * @property int $created_by
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamAnswer> $answers
 * @property-read int|null $answers_count
 * @property-read Quiz $quiz
 * @property-read Teacher $user
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam query()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereQuizId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereUserId($value)
 * @mixin \Eloquent
 */
class Exam extends Model
{
    use HasFactory;

    protected $table = 'exam';
    public $timestamps = true;

    // App/Models/Exam.php
    protected $fillable = [
        'subject_id',
        'quiz_id',
        'user_id',
        'created_by',
        'updated_by',
        // Boshqa barcha mass assignment qilinadigan ustunlarni shu yerga qo'shing
    ];

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }


    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(Teacher::class);
    }


    public static function correctCount(string $id)
    {
        // Natijalarni hisoblash
        $correctAnswersCount = 0;
        // $incorrectAnswersCount = 0;
        $examAnswers = ExamAnswer::where('exam_id', '=', $id)->get();
        $totalQuestions = count($examAnswers);

        foreach ($examAnswers as $answer) {
            $option = Option::find($answer->option_id);
            if ($option && $option->is_correct == 1) {
                $correctAnswersCount++;
            } else {
                // $incorrectAnswersCount++;
            }
        }

        return $correctAnswersCount;
    }

    public static function inCorrectCount(string $id)
    {
        // Natijalarni hisoblash
        // $correctAnswersCount = 0;
        $incorrectAnswersCount = 0;
        $examAnswers = ExamAnswer::where('exam_id', '=', $id)->get();
        $totalQuestions = count($examAnswers);

        foreach ($examAnswers as $answer) {
            $option = Option::find($answer->option_id);
            if ($option && $option->is_correct != 1) {
                $incorrectAnswersCount++;
            } else {
                // $incorrectAnswersCount++;
            }
        }

        return $incorrectAnswersCount;
    }

    public static function allQuestions(string $id)
    {
        // Natijalarni hisoblash
        // $correctAnswersCount = 0;
        // $incorrectAnswersCount = 0;
        $exam = Exam::findOrFail($id);
        $quiz = Quiz::findOrFail($exam->quiz_id)->questions;

        // $examAnswers = ExamAnswer::where('exam_id', '=', $id)->get();
        $totalQuestions = count($quiz);



        return $totalQuestions;
    }
}
