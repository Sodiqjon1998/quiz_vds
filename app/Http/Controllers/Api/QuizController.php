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
use Illuminate\Support\Facades\Cache;

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

            // ✅ Attachment'ni cache'lash
            $attachment = Cache::remember("quiz_{$quizId}_attachment", 3600, function () use ($quizId) {
                return Attachment::getAttamptById($quizId);
            });

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

                // ✅ YANGI KOD SHU YERDA
                $cacheKey = "quiz_{$quizId}_questions";

                $questions = Cache::remember($cacheKey, 3600, function () use ($quizId) {
                    \Log::info("Quiz {$quizId} savollar DB'dan yuklanmoqda");

                    return Question::where('quiz_id', $quizId)
                        ->where('status', Question::STATUS_ACTIVE)
                        ->with('options')
                        ->get();
                });

                // Tasodifiy tartibda (cache'dan keyin)
                $questions = $questions->shuffle()->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question_text' => $question->question,
                        'mark' => $question->mark,
                        'options' => $question->options->shuffle()->map(function ($option) {
                            return [
                                'id' => $option->id,
                                'option_text' => $option->option,
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
        try {
            \Log::info('Submit Quiz Started', [
                'subject_id' => $subjectId,
                'quiz_id' => $quizId,
                'user_id' => $request->user()->id,
                'request_data' => $request->all()
            ]);

            // ✅ Validatsiya
            $validated = $request->validate([
                'answers' => 'required|array',
            ]);

            \Log::info('Validation passed', ['validated' => $validated]);

            $user = $request->user();

            // ✅ Urinishlarni tekshirish (cache'dan)
            $attachment = Cache::remember("quiz_{$quizId}_attachment", 3600, function () use ($quizId) {
                return Attachment::getAttamptById($quizId);
            });

            \Log::info('Attachment found', ['attachment' => $attachment]);

            if (!$attachment) {
                \Log::error('Attachment not found', ['quiz_id' => $quizId]);
                return response()->json([
                    'success' => false,
                    'error' => 'NOT_FOUND',
                    'message' => "Imtihon ma'lumoti topilmadi."
                ], 404);
            }

            $examAttachmentCount = Exam::where('quiz_id', $quizId)
                ->where('user_id', $user->id)
                ->where('subject_id', $subjectId)
                ->count();

            \Log::info('Exam count', [
                'count' => $examAttachmentCount,
                'allowed' => $attachment->number
            ]);

            if ($attachment->number <= $examAttachmentCount) {
                return response()->json([
                    'success' => false,
                    'error' => 'NO_ATTEMPTS',
                    'message' => "Urunishlar qolmadi"
                ], 403);
            }

            $totalScore = 0;
            $correctAnswers = 0;
            $detailedResults = [];

            // ✅ Bir xil cache'dan olish (tezroq!)
            $cacheKey = "quiz_{$quizId}_questions";

            $questions = Cache::remember($cacheKey, 3600, function () use ($quizId) {
                \Log::info("Submit: Quiz {$quizId} savollar DB'dan yuklanmoqda");

                return Question::where('quiz_id', $quizId)
                    ->where('status', Question::STATUS_ACTIVE)
                    ->with('options')
                    ->get();
            });

            \Log::info('Questions loaded', ['count' => $questions->count()]);

            foreach ($questions as $question) {
                $selectedOptionId = $validated['answers'][$question->id] ?? null;

                $correctOption = $question->options()->where('is_correct', true)->first();
                $isCorrect = false;

                if ($selectedOptionId && $correctOption && $selectedOptionId == $correctOption->id) {
                    $totalScore += (int)$question->mark;
                    $correctAnswers++;
                    $isCorrect = true;
                }

                // Batafsil natijalar
                $detailedResults[] = [
                    'question_id' => $question->id,
                    'question_text' => strip_tags($question->question ?? $question->name ?? ''),
                    'question_image' => $question->image,
                    'selected_option_id' => $selectedOptionId,
                    'correct_option_id' => $correctOption ? $correctOption->id : null,
                    'is_correct' => $isCorrect,
                    'mark' => (int)$question->mark,
                ];
            }

            \Log::info('Processing complete', [
                'total_score' => $totalScore,
                'correct_answers' => $correctAnswers
            ]);

            // Exam yaratish
            $exam = Exam::create([
                'quiz_id' => $quizId,
                'subject_id' => $subjectId,
                'user_id' => $user->id,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            \Log::info('Exam created', ['exam_id' => $exam->id]);

            // Javoblarni saqlash
            $answers = [];
            foreach ($questions as $question) {
                $selectedOptionId = $validated['answers'][$question->id] ?? null;

                if ($selectedOptionId) {
                    $answers[] = [
                        'exam_id' => $exam->id,
                        'question_id' => $question->id,
                        'option_id' => $selectedOptionId,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // ✅ Bir marta yozish (tezroq!)
            \DB::table('exam_answer')->insert($answers);

            \Log::info('Answers saved');

            $totalQuestions = $questions->count();
            $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
            $passed = $percentage >= 70;

            return response()->json([
                'success' => true,
                'message' => 'Quiz muvaffaqiyatli topshirildi!',
                'data' => [
                    'score' => $correctAnswers,
                    'total_questions' => $totalQuestions,
                    'percentage' => $percentage,
                    'passed' => $passed,
                    'detailed_results' => $detailedResults
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error', [
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'VALIDATION_ERROR',
                'message' => 'Validatsiya xatosi',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Quiz submit error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'SERVER_ERROR',
                'message' => config('app.debug') ? $e->getMessage() : 'Server xatosi yuz berdi',
                'debug' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }
}
