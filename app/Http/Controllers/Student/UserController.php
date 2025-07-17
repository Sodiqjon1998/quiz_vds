<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\Question;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $student = Auth::user(); // Kirgan talabaning ma'lumotlarini olish

        // Talabaning so'nggi testlarini olish (masalan, oxirgi 5 ta)
        // E'tibor bering: 'total_questions' va 'correct_answers' Exam modelida atribut sifatida mavjud bo'lishi kerak
        // Yoki buni hisoblash uchun munosabatlardan foydalanishingiz kerak.
        $recentExams = Exam::where('user_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Har bir exam uchun to'g'ri javoblarni hisoblash (agar Exam modelida mavjud bo'lmasa)
        // Yuqoridagi eager loading (with(['question', 'option'])) tavsiyasini qo'llagan bo'lsangiz, bu qismni optimallashtirishingiz mumkin.
        foreach ($recentExams as $exam) {
            // Agar Exam modelida to'g'ri javoblar sonini saqlovchi ustun bo'lmasa
            // Bu qism ExamAnswer modelidan ma'lumotlarni olish uchun o'zgartirilishi kerak
            // Masalan:
            $exam->correct_answers = ExamAnswer::where('exam_id', $exam->id)
                ->join('option', 'exam_answer.option_id', '=', 'option.id')
                ->where('option.is_correct', 1)
                ->count();
            $exam->total_questions = Question::where('quiz_id', '=', $exam->quiz_id)->count();

            // Agar siz allaqachon Exam modelida hisoblagan bo'lsangiz, yuqoridagi kodga ehtiyoj yo'q
            // Va shunchaki $exam->correct_answers va $exam->total_questions ga ishora qilsangiz bo'ladi.
            // Bu qism sizning ma'lumotlar bazasi strukturangizga bog'liq.
        }
        return view('student.user.index', [
            'student' => $student,
            'recentExams' => $recentExams,
        ]);
    }



    public function show(string $id)
    {
        $exam = Exam::findOrFail($id);
        $examAnswers = ExamAnswer::where('exam_id', '=', $id)->get();

        return view('student.user.show', [
            'exam' => $exam,
            'examAnswers' => $examAnswers,
        ]);
    }
}
