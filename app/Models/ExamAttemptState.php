<?php

// App/Models/ExamAttemptState.php

namespace App\Models;

use App\Models\Student\Quiz;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $quiz_id
 * @property int $current_question_index
 * @property int|null $remaining_time
 * @property array|null $user_answers
 * @property array|null $question_statuses
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Quiz $quiz
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState whereCurrentQuestionIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState whereQuestionStatuses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState whereQuizId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState whereRemainingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState whereUserAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamAttemptState whereUserId($value)
 * @mixin \Eloquent
 */
class ExamAttemptState extends Model
{
    use HasFactory;

    protected $table = 'exam_attempt_states'; // Jadval nomi

    protected $fillable = [
        'user_id',
        'quiz_id',
        'current_question_index',
        'remaining_time',
        'user_answers',
        'question_statuses',
    ];

    protected $casts = [
        'user_answers' => 'array', // Avtomatik arrayga aylantirish
        'question_statuses' => 'array', // Avtomatik arrayga aylantirish
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}

