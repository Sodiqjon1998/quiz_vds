<?php

namespace App\Models;

use App\Models\Student\Quiz;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property-read Quiz|null $quiz
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|QuizAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuizAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuizAttempt query()
 * @mixin \Eloquent
 */
class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'answers',
        'current_question_index',
        'time_left',
        'is_completed',
    ];

    protected $casts = [
        'answers' => 'array', // JSON ustunini avtomatik arrayga o'tkazish uchun
        'is_completed' => 'boolean',
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
