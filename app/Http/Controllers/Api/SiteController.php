<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Student\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Users;
use Hash;

class AuthController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        // Foydalanuvchi quizlarini olish
        $quizzes = Quiz::select([
            'quiz.id as quizId',
            'quiz.name as quizName',
            'quiz.subject_id as quizSubjectId',
            'quiz.classes_id as quizClassesId',
            'quiz.status as quizStatus',
            'subjects.id as subjectId',
            'subjects.name as subjectName',
            'classes.id as classesId',
            'classes.name as classesName',
            'attachment.date',
            'attachment.number',
            'attachment.time'
        ])
            ->leftJoin('subjects', 'subjects.id', '=', 'quiz.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'quiz.classes_id')
            ->leftJoin('attachment', 'attachment.quiz_id', '=', 'quiz.id')
            ->where('classes.id', $user->classes_id) // Foydalanuvchi klassi
            ->where('quiz.status', '=', 1) // Aktiv quizlar
            ->get();

        // Har bir quiz uchun urinishlar sonini hisoblash
        $quizzesWithAttempts = $quizzes->map(function ($quiz) use ($user) {
            $attemptCount = Exam::where('quiz_id', $quiz->quizId)
                ->where('user_id', $user->id)
                ->where('subject_id', $quiz->quizSubjectId)
                ->count();

            return [
                'id' => $quiz->quizId,
                'name' => $quiz->quizName,
                'subject' => [
                    'id' => $quiz->subjectId,
                    'name' => $quiz->subjectName
                ],
                'class' => [
                    'id' => $quiz->classesId,
                    'name' => $quiz->classesName
                ],
                'date' => $quiz->date,
                'time' => $quiz->time,
                'attempts' => [
                    'used' => $attemptCount,
                    'total' => $quiz->number,
                    'remaining' => max(0, $quiz->number - $attemptCount)
                ],
                'status' => $this->getQuizStatus($quiz->date, $attemptCount, $quiz->number)
            ];
        });

        // Statistika
        $totalQuizzes = $quizzes->count();
        $completedQuizzes = $quizzesWithAttempts->where('attempts.used', '>', 0)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'quizzes' => $quizzesWithAttempts,
                'statistics' => [
                    'total' => $totalQuizzes,
                    'completed' => $completedQuizzes,
                    'remaining' => $totalQuizzes - $completedQuizzes
                ]
            ]
        ]);
    }
}
