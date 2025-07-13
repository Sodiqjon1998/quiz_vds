<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Models\Teacher\Quiz;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Http\Controllers\Controller;
use App\Models\Subjects;
use Auth;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Quiz::find()->paginate(20);
        return view('teacher.exam.index', [
            'model' => $model,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getResult(Request $request)
    {
        // Foydalanuvchi qaysi fanga tegishli ekanligini olamiz
        $loggedInSubjectId = Auth::user()->subject_id;

        // URL orqali kelgan subject_id ni olamiz. Agar kelmasa, foydalanuvchining o'z fanini olamiz.
        $selectedSubjectId = $request->input('subject_id', $loggedInSubjectId);

        // Barcha fanlar ro'yxatini yuklaymiz (filtr uchun)
        $subjects = Subjects::all(); // Yoki Subject::where('id', $loggedInSubjectId)->get(); agar faqat o'z fanlarini ko'rishi kerak bo'lsa

        // Imtihonlarni yuklaymiz
        // Eager loading: answers, quiz, user, quiz.questions.options munosabatlarini yuklaymiz
        // 'answers' munosabatidagi 'option' ni ham yuklaymiz, chunki answer.option_id ni tekshirish uchun option modelidagi is_correct kerak
        $examsQuery = Exam::with([
            'answers.question.options', // Javobning savoli va uning variantlari
            'user', // Kim test topshirgan
            'quiz', // Qaysi test ekanligi
        ]);

        // Agar ma'lum bir fan tanlangan bo'lsa, uni filtrga qo'shamiz
        if ($selectedSubjectId) {
            $examsQuery->where('subject_id', '=', $selectedSubjectId);
        } else {
            // Agar hech qanday fan tanlanmagan bo'lsa, kirgan foydalanuvchining faniga tegishli imtihonlarni ko'rsatamiz
            $examsQuery->where('subject_id', '=', $loggedInSubjectId);
        }

        // Pagination bilan imtihonlarni olamiz
        $exams = $examsQuery->paginate(20);

        // Har bir imtihon uchun to'g'ri, noto'g'ri va javobsiz qolgan savollar sonini hisoblaymiz
        foreach ($exams as $exam) {
            $correctAnswersCount = 0;
            $incorrectAnswersCount = 0;
            $totalQuestionsCount = 0; // Bu testdagi jami savollar soni

            // Quizga tegishli savollar sonini olamiz
            if ($exam->quiz && $exam->quiz->questions) {
                $totalQuestionsCount = $exam->quiz->questions->count();
            }

            // Foydalanuvchining har bir javobini tekshiramiz
            foreach ($exam->answers as $answer) {
                // Agar javobga bog'langan savol va uning variantlari mavjud bo'lsa
                if ($answer->question && $answer->question->options) {
                    // Berilgan savol uchun to'g'ri variantni topamiz
                    $correctOption = $answer->question->options->firstWhere('is_correct', true);

                    // Agar to'g'ri variant topilsa va foydalanuvchining javobi to'g'ri variantga mos kelsa
                    if ($correctOption && $answer->option_id === $correctOption->id) {
                        $correctAnswersCount++;
                    } else {
                        $incorrectAnswersCount++;
                    }
                }
            }
            // Javobsiz qolgan savollar
            $unansweredQuestionsCount = $totalQuestionsCount - $correctAnswersCount - $incorrectAnswersCount;
            if ($unansweredQuestionsCount < 0) {
                // Manfiy son chiqmasligi uchun
                $unansweredQuestionsCount = 0;
            }

            // Hisoblangan natijalarni Exam obyektiga qo'shamiz
            $exam->correct_answers_count = $correctAnswersCount;
            $exam->incorrect_answers_count = $incorrectAnswersCount;
            $exam->unanswered_questions_count = $unansweredQuestionsCount;
            $exam->total_questions_in_quiz = $totalQuestionsCount; // Testdagi jami savollar soni
        }

        return view('teacher.exam.result', [
            'subjectId' => $selectedSubjectId, // Filtr uchun tanlangan fan ID
            'exams' => $exams,
            'subjects' => $subjects, // Barcha fanlar ro'yxati
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $quiz_id, string $subject_id)
    {
        $model = Exam::where('quiz_id', $quiz_id)->where('subject_id', $subject_id)->paginate(20);

        return view('teacher.exam.show', [
            'model' => $model,
        ]);
    }

    public function showTest(string $id)
    {
        $exam = Exam::findOrFail($id);
        $examAnswers = ExamAnswer::where('exam_id', '=', $id)->get();

        return view('teacher.exam.showTest', [
            'exam' => $exam,
            'examAnswers' => $examAnswers,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
