<?php

namespace App\Http\Controllers\Api;

use App\Events\DuelAccepted;
use App\Events\DuelChallenge;
use App\Http\Controllers\Controller;
use App\Models\Subjects;
use App\Models\Question;
use App\Models\Exam;
use App\Models\Attachment;
use App\Models\Student\Quiz;
use App\Models\Users;
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


    /**
     * Duel rejimi uchun maxsus metod.
     * Bu metodda to'g'ri javoblar (is_correct) OCHIQ holda yuboriladi.
     */
    public function getDuelQuestions($subjectId, $quizId)
    {
        // ⚠️ DIQQAT: inRandomOrder() ni OLIB TASHLANG!
        // Uning o'rniga orderBy('id') ishlating.

        $questions = \App\Models\Question::where('quiz_id', $quizId)
            ->with('options')
            ->orderBy('id', 'asc') // <--- MANA SHU NARSANI QO'SHING (Random emas!)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'questions' => $questions
            ]
        ]);
    }


    /**
     * Duel sahifasi uchun barcha quizlar ro'yxatini qaytaradi.
     */
    public function getDuelQuizzes(Request $request)
    {
        try {
            // Student Quiz modelidan foydalanamiz
            $quizzes = \App\Models\Student\Quiz::with('subject')
                ->where('status', Quiz::STATUS_ACTIVE) // Faqat faol quizlar (agar status ustuni bo'lsa)
                ->orderBy('id', 'desc')
                ->get();

            $data = $quizzes->map(function ($quiz) {
                return [
                    'id' => $quiz->id,
                    'name' => $quiz->name,
                    'class' => $quiz->class,
                    'subject' => [
                        'id' => $quiz->subject->id,
                        'name' => $quiz->subject->name ?? 'Fan nomi yo\'q'
                    ],
                    // Savollar sonini ham qo'shish foydali bo'ladi
                    'questions_count' => \App\Models\Question::where('quiz_id', $quiz->id)->count()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server xatosi: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * O'quvchining sinfdoshlarini ro'yxatini qaytaradi (O'zidan tashqari)
     */
    /**
     * O'quvchining sinfdoshlarini ro'yxatini qaytaradi (O'zidan tashqari)
     */
    public function getClassmates(Request $request)
    {
        try {
            $user = $request->user();

            // classes_id array ekanligini hisobga olamiz
            $classIds = $user->classes_id;

            if (empty($classIds)) {
                return response()->json([
                    'success' => false,
                    'message' => "Siz hech qaysi sinfga biriktirilmagansiz."
                ], 404);
            }

            // Queryni boshlaymiz
            $query = \App\Models\Users::query();

            // 1. O'zini ro'yxatdan chiqarib tashlash
            $query->where('id', '!=', $user->id);

            // 2. Faqat o'quvchilarni olish (user_type = 4 bu Student)
            $query->where('user_type', \App\Models\Users::TYPE_STUDENT);

            // 3. Sinfdoshi ekanligini tekshirish (JSON qidiruv)
            $query->where(function ($q) use ($classIds) {
                // Agar $classIds massiv bo'lsa
                if (is_array($classIds)) {
                    foreach ($classIds as $id) {
                        // JSON ichidan qidirish (MySQL/PostgreSQL)
                        $q->orWhereJsonContains('classes_id', $id);
                        // Yoki oddiy string qidiruv (agar baza JSON support qilmasa)
                        $q->orWhere('classes_id', 'like', '%"' . $id . '"%');
                    }
                } else {
                    // Agar string yoki son bo'lsa
                    $q->where('classes_id', 'like', '%"' . $classIds . '"%')
                        ->orWhere('classes_id', $classIds);
                }
            });

            // Kerakli ustunlarni olish ('img' to'g'ri nom)
            $classmates = $query->select('id', 'first_name', 'last_name', 'img')->get();

            // Ma'lumotlarni formatlash
            $data = $classmates->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->first_name . ' ' . $student->last_name,
                    // 'img' ustunini tekshiramiz
                    'avatar' => $student->img ? asset('storage/' . $student->img) : null,
                    'short_name' => $student->first_name
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            // Xatolikni log faylga yozish (debug uchun foydali)
            \Log::error('GetClassmates Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server xatosi: ' . $e->getMessage()
            ], 500);
        }
    }


    // 1. Chaqiruv yuborish
    public function sendChallenge(Request $request)
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'quiz_id' => 'required|exists:quiz,id',
            'subject_id' => 'required|exists:subjects,id'
        ]);

        $challenger = auth()->user();
        $target = Users::find($request->target_user_id);

        // ✅ Event yuborishda quiz_id va subject_id ni to'g'ri uzatish
        broadcast(new DuelChallenge(
            $challenger,
            $target,
            $request->quiz_id,
            $request->subject_id
        ))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Duelga chaqiruv yuborildi'
        ]);
    }

    // 2. Chaqiruvni qabul qilish
    public function acceptChallenge(Request $request)
    {
        $request->validate([
            'challenger_id' => 'required|exists:users,id',
            'quiz_id' => 'required|integer',  // ✅ QO'SHILDI
            'subject_id' => 'required|integer'  // ✅ QO'SHILDI
        ]);

        $accepter = auth()->user();
        $challenger = Users::find($request->challenger_id);

        // ✅ Request dan to'g'ridan-to'g'ri olish
        $quizId = $request->quiz_id;
        $subjectId = $request->subject_id;

        // ✅ CHALLENGER ga event yuborish
        broadcast(new DuelAccepted(
            $accepter,
            $challenger,
            $quizId,
            $subjectId
        ))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Duel qabul qilindi',
            'quiz_id' => $quizId,
            'subject_id' => $subjectId
        ]);
    }


    // Ballni yangilash va raqibga yuborish
    public function updateDuelScore(Request $request)
    {
        $user = $request->user();
        $opponentId = $request->input('opponent_id');
        $score = $request->input('score');

        // Event orqali raqibga xabar yuboramiz
        broadcast(new \App\Events\DuelScoreUpdated($user->id, $score, $opponentId));

        return response()->json(['success' => true]);
    }

    public function duelGameState(Request $request)
    {
        $user = $request->user();
        $opponentId = $request->input('opponent_id');
        $type = $request->input('type');
        $data = $request->input('data');

        // ✅ YANGI QO'SHILGAN QATOR: Javob bergan odamni belgilab qo'yamiz
        $data['actor_id'] = $user->id;

        // 1. Agar bu javob berish holati bo'lsa, QULF qo'yamiz
        if ($type === 'answer') {
            $ids = [$user->id, $opponentId];
            sort($ids);
            $matchKey = implode('_', $ids);

            $qIndex = $data['question_index'] ?? 0;
            $lockKey = "duel_lock_{$matchKey}_q_{$qIndex}";

            if (!\Illuminate\Support\Facades\Cache::add($lockKey, 1, 5)) {
                return response()->json(['success' => false, 'message' => 'Too late']);
            }
        }

        // Qolgan kod o'zgarishsiz...
        broadcast(new \App\Events\DuelGameState($opponentId, $type, $data));
        broadcast(new \App\Events\DuelGameState($user->id, $type, $data));

        return response()->json(['success' => true]);
    }
}
