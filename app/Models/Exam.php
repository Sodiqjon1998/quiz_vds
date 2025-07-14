<?php

namespace App\Models;

use App\Models\Teacher\Quiz;
use App\Models\Teacher\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
