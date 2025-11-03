<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subjects;
use App\Models\Question;
use App\Models\Exam;
use App\Models\Attachment;
use App\Models\Student\Quiz;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Request $request, $subjectId, $quizId)
    {
        try {
            $user = $request->user(); // Sanctum auth user

            // Subject va Quiz tekshirish
            $subject = Subjects::findOrFail($subjectId);
            $quiz = Quiz::with('attachment')->findOrFail($quizId);

            // Urinishlar sonini tekshirish
            $examAttachmentCount = Exam::where('quiz_id', $quizId)
                ->where('user_id', $user->id)
                ->where('subject_id', $subjectId)
                ->count();

            $attachment = Attachment::getAttamptById($quizId);

            if (!$attachment) {
                return response()->json([
                    'success' => false,
                    'error' => 'NOT_FOUND',
                    'message' => "Imtihon ma'lumoti topilmadi."
                ], 404);
            }

            // Urinishlar tugagan bo'lsa
            if ($attachment->number <= $examAttachmentCount) {
                return response()->json([
                    'success' => false,
                    'error' => 'NO_ATTEMPTS',
                    'message' => "Urunishlar qolmadi",
                    'date' => $attachment->date,
                    'attempts_left' => 0,
                    'total_attempts' => $attachment->number
                ], 403);
            }

            $examDate = Carbon::parse($attachment->date);
            $today = Carbon::today();

            // Sana tekshirish
            if ($examDate->isToday()) {
                // Savollarni olish (tasodifiy tartibda)
                $questions = Question::where('quiz_id', $quizId)
                    ->where('status', Question::STATUS_ACTIVE)
                    ->with('options')
                    ->inRandomOrder()
                    ->get()
                    ->map(function ($question) {
                        return [
                            'id' => $question->id,
                            'question_text' => $question->question,
                            'mark' => $question->mark,
                            'options' => $question->options->shuffle()->map(function ($option) {
                                return [
                                    'id' => $option->id,
                                    'option_text' => $option->option,
                                    // is_correct ni yo'qotamiz (xavfsizlik uchun)
                                ];
                            })
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'data' => [
                        'quiz' => [
                            'id' => $quiz->id,
                            'name' => $quiz->name,
                            'description' => $quiz->description,
                            'duration' => $quiz->duration, // minut
                        ],
                        'subject' => [
                            'id' => $subject->id,
                            'name' => $subject->name,
                        ],
                        'questions' => $questions,
                        'attempts_left' => $attachment->number - $examAttachmentCount,
                        'total_attempts' => $attachment->number,
                        'exam_date' => $attachment->date,
                    ]
                ], 200);
            } elseif ($examDate->isFuture()) {
                return response()->json([
                    'success' => false,
                    'error' => 'NOT_STARTED',
                    'message' => "Qo'yilgan imtihon vaqti kelmadi",
                    'date' => $attachment->date
                ], 403);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'EXPIRED',
                    'message' => "Qo'yilgan imtihon vaqti tugadi!",
                    'date' => $attachment->date
                ], 403);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'NOT_FOUND',
                'message' => 'Quiz yoki Subject topilmadi'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'SERVER_ERROR',
                'message' => 'Server xatosi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Quiz natijasini yuborish
    public function submitQuiz(Request $request, $subjectId, $quizId)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.option_id' => 'required|exists:options,id',
        ]);

        $user = $request->user();

        // Javoblarni tekshirish va ball hisoblash
        $totalScore = 0;
        $correctAnswers = 0;

        foreach ($validated['answers'] as $answer) {
            $question = Question::findOrFail($answer['question_id']);
            $option = $question->options()->where('id', $answer['option_id'])->first();

            if ($option && $option->is_correct) {
                $totalScore += $question->mark;
                $correctAnswers++;
            }
        }

        // Natijani saqlash
        $exam = Exam::create([
            'quiz_id' => $quizId,
            'subject_id' => $subjectId,
            'user_id' => $user->id,
            'score' => $totalScore,
            'total_questions' => count($validated['answers']),
            'correct_answers' => $correctAnswers,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quiz muvaffaqiyatli topshirildi!',
            'result' => [
                'score' => $totalScore,
                'total_questions' => count($validated['answers']),
                'correct_answers' => $correctAnswers,
                'percentage' => round(($correctAnswers / count($validated['answers'])) * 100, 2)
            ]
        ], 200);
    }
}
