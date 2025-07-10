<?php

// App/Models/ExamAttemptState.php

namespace App\Models;

use App\Models\Student\Quiz;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttemptState extends Model
{
    use HasFactory;

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

