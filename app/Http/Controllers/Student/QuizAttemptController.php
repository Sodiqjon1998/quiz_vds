<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Models\Exam; // Exam modelini import qilish
use App\Models\ExamAnswer; // ExamAnswer modelini import qilish
use Illuminate\Support\Facades\Auth;
use Request;

class QuizAttemptController extends Controller
{
    /**
     * Test holatini (javoblar, joriy savol, qolgan vaqt) serverga saqlash.
     * Bu funksiya AJAX so'rovlari orqali, test davomida doimiy chaqiriladi.
     */
    public function saveAttempt(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = Auth::user();
        $quizId = $request->input('quiz_id');
        $answersData = $request->input('answers'); // questionStatuses, userAnswers, markedForReview
        $currentQuestionIndex = $request->input('current_question_index', 0);
        $timeLeft = $request->input('time_left');
        $isCompleted = $request->input('is_completed', false); // Frontenddan keladigan holat

        // QuizAttempt ni topish yoki yangisini yaratish
        $attempt = QuizAttempt::firstOrCreate(
            [
                'user_id' => $user->id,
                'quiz_id' => $quizId,
                'is_completed' => false, // Faqat tugallanmagan urinishni topish
            ],
            [
                'current_question_index' => 0,
                'time_left' => $timeLeft,
            ]
        );

        // Mavjud urinishni yangilash
        $attempt->answers = $answersData;
        $attempt->current_question_index = $currentQuestionIndex;
        $attempt->time_left = $timeLeft;
        $attempt->is_completed = $isCompleted; // Frontenddan keladigan yakunlanganlik holati
        $attempt->save();

        return response()->json(['message' => 'Quiz attempt saved successfully!'], 200);
    }

    /**
     * Test holatini serverdan yuklash.
     */
    public function loadAttempt(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = Auth::user();
        $quizId = $request->input('quiz_id');

        $attempt = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->where('is_completed', false)
            ->first();

        if ($attempt) {
            return response()->json([
                'answers' => $attempt->answers,
                'current_question_index' => $attempt->current_question_index,
                'time_left' => $attempt->time_left,
            ]);
        }

        return response()->json(['message' => 'No active quiz attempt found.'], 404);
    }

    /**
     * Testni yakunlash va natijalarni Exam va ExamAnswer modellariga saqlash.
     * Bu metod, foydalanuvchi testni tugatganda yoki vaqt tugaganda chaqiriladi.
     */
    public function submitQuiz(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = Auth::user();
        $quizId = $request->input('quiz_id');

        // Avval, QuizAttemptdan oxirgi saqlangan holatni olamiz
        $attempt = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->where('is_completed', false) // Faqat tugallanmagan urinishni yakunlaymiz
            ->first();

        if (!$attempt) {
            return response()->json(['message' => 'No active quiz attempt found to submit.'], 404);
        }

        // Frontenddan kelgan javoblar (bu sizning oldingi AJAX so'rovingizdagi 'answers' ga to'g'ri keladi)
        $userAnswers = $attempt->answers['userAnswers'] ?? []; // QuizAttemptdagi saqlangan javoblar

        // Exam jadvaliga ma'lumotlarni saqlash
        $examId = Exam::insertGetId([
            'subject_id' => $request->input('subject_id'), // Frontenddan kelishi kerak
            'quiz_id' => $quizId,
            'user_id' => $user->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'created_at' => now(), // created_at va updated_at ni qo'lda qo'shish kerak agar timestamps yo'q bo'lsa
            'updated_at' => now(),
        ]);

        // ExamAnswer jadvaliga har bir savol javobini saqlash
        foreach ($userAnswers as $questionId => $optionId) {
            // $optionId bu yerda variantning indeksi emas, balki to'g'ridan-to'g'ri option_id bo'lishi kerak.
            // Agar frontenddan option_id kelmasa, uni question_id va tanlangan indeks orqali topish kerak.
            // Masalan:
            $selectedOption = \App\Models\Option::where('question_id', $questionId)->get()[$optionId] ?? null;
            if ($selectedOption) {
                ExamAnswer::insert([
                    'exam_id' => $examId,
                    'question_id' => $questionId,
                    'option_id' => $selectedOption->id, // Variantning ID'sini saqlash
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Test tugallanganligini belgilash
        $attempt->is_completed = true;
        $attempt->save();

        // Natijalar sahifasiga yo'naltirish (bu AJAX bo'lgani uchun, frontendda yo'naltirish kerak)
        return response()->json([
            'message' => 'Quiz submitted successfully!',
            'exam_id' => $examId,
            'redirect_url' => route('student.quiz.result', $examId) // Frontend uchun URL
        ], 200);
    }
}
