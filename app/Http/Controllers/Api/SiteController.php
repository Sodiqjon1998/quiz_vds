<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher\Attachment;
use App\Models\Teacher\Quiz;
use App\Models\Teacher\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // ✅ Carbon qo'shildi

class SiteController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $today = Carbon::today();

            // ✅ Quiz'larni to'liq ma'lumot bilan olish
            $quizzes = DB::table('quiz')
                ->select([
                    'quiz.id',
                    'quiz.name as quiz_name',
                    'quiz.status as quiz_status',
                    'quiz.subject_id',
                    'quiz.classes_id',
                    'subjects.name as subject_name',
                    'classes.name as class_name',
                    'attachment.id as attachment_id',
                    'attachment.date as attachment_date',
                    'attachment.time as attachment_time',
                    'attachment.number as attachment_number',
                    'attachment.status as attachment_status',
                ])
                ->leftJoin('subjects', 'subjects.id', '=', 'quiz.subject_id')
                ->leftJoin('classes', 'classes.id', '=', 'quiz.classes_id')
                ->leftJoin('attachment', 'attachment.quiz_id', '=', 'quiz.id')
                ->where('quiz.classes_id', '=', $user->classes_id)
                ->where('quiz.status', 1)
                ->paginate(20);

            $processedQuizzes = $quizzes->map(function ($quiz) use ($user, $today) {
                // Urinishlarni hisoblash
                $usedAttempts = DB::table('exam')
                    ->where('user_id', $user->id)
                    ->where('quiz_id', $quiz->id)
                    ->count();

                // Sana tekshiruvi
                $quizDate = $quiz->attachment_date ? Carbon::parse($quiz->attachment_date) : null;
                $isExpired = $quizDate && $quizDate->lt($today);
                $isUpcoming = $quizDate && $quizDate->gt($today);

                return [
                    'id' => $quiz->id,
                    'name' => $quiz->quiz_name,
                    'subject' => [
                        'id' => $quiz->subject_id,
                        'name' => $quiz->subject_name
                    ],
                    'class' => $quiz->class_name,
                    'status' => $quiz->quiz_status,
                    // ✅ Attachment ma'lumotlari
                    'attachment' => $quiz->attachment_id ? [
                        'id' => $quiz->attachment_id,
                        'date' => $quiz->attachment_date,
                        'time' => $quiz->attachment_time ?? '00:30:00',
                        'number' => $quiz->attachment_number ?? 3,
                        'status' => $quiz->attachment_status,
                    ] : null,
                    // ✅ Holat ko'rsatkichlari
                    'is_expired' => $isExpired,
                    'is_upcoming' => $isUpcoming,
                    'is_available' => !$isExpired && !$isUpcoming,
                    // ✅ Urinishlar
                    'attempts' => [
                        'used' => $usedAttempts,
                        'total' => $quiz->attachment_number ?? 3,
                        'remaining' => max(0, ($quiz->attachment_number ?? 3) - $usedAttempts),
                    ],
                ];
            });

            $statistics = [
                'total' => DB::table('quiz')
                    ->where('classes_id', $user->classes_id)
                    ->where('status', 1)
                    ->count(),
                'completed' => DB::table('exam')
                    ->where('user_id', $user->id)
                    ->distinct('quiz_id')
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Quizlar ro\'yxati muvaffaqiyatli olindi.',
                'data' => [
                    'quizzes' => $processedQuizzes,
                    'statistics' => $statistics
                ],
                'pagination' => [
                    'total' => $quizzes->total(),
                    'current_page' => $quizzes->currentPage(),
                    'last_page' => $quizzes->lastPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Index Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    public function start(Request $request, $quizId)
    {
        try {
            $user = Auth::user();
            $today = Carbon::today(); // ✅ Bugungi sana

            // Quiz mavjudligini tekshirish
            $quiz = Quiz::with(['subject', 'class'])->find($quizId);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz topilmadi.'
                ], 404);
            }

            // Attachment ma'lumotlarini olish
            $attachment = DB::table('attachment')
                ->where('quiz_id', $quizId)
                ->first();

            if (!$attachment) {
                $attachmentId = DB::table('attachment')->insertGetId([
                    'quiz_id' => $quizId,
                    'date' => now()->toDateString(),
                    'time' => '00:30:00',
                    'number' => 3,
                    'status' => 1,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $attachment = DB::table('attachment')->find($attachmentId);
            }

            // ✅ MUHIM: Test sanasini tekshirish
            if ($attachment->date) {
                $quizDate = Carbon::parse($attachment->date);

                // Agar test sanasi bugundan oldin bo'lsa
                if ($quizDate->lt($today)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bu test muddati tugagan. Test sanasi: {$attachment->date}. Testni boshlash imkonsiz."
                    ], 403);
                }

                // Agar test sanasi bugundan keyin bo'lsa
                if ($quizDate->gt($today)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bu test hali boshlanmagan. Test sanasi: {$attachment->date}. Iltimos, belgilangan sanada qaytib keling."
                    ], 403);
                }
            }

            // Urinishlarni tekshirish
            $maxAttempts = $attachment->number ?? 3;

            $usedAttempts = DB::table('exam')
                ->where('user_id', $user->id)
                ->where('quiz_id', $quizId)
                ->count();

            if ($usedAttempts >= $maxAttempts) {
                return response()->json([
                    'success' => false,
                    'message' => "Siz ushbu quizdan maksimal urinishlar sonidan ({$maxAttempts}) foydalanganmisiz."
                ], 403);
            }

            // Savollarni olish
            $questions = Question::where('quiz_id', $quizId)
                ->with('options:id,question_id,name')
                ->select('id', 'name', 'image', 'quiz_id')
                ->inRandomOrder()
                ->get()
                ->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'name' => $question->name,
                        'image' => $question->image ? asset('storage/' . $question->image) : null,
                        'quiz_id' => $question->quiz_id,
                        'options' => $question->options->map(function ($option) {
                            return [
                                'id' => $option->id,
                                'name' => $option->name,
                            ];
                        })
                    ];
                });

            if ($questions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu quizda hozircha savollar mavjud emas.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Quiz muvaffaqiyatli boshlandi.',
                'data' => [
                    'quiz_details' => [
                        'id' => $quiz->id,
                        'name' => $quiz->name,
                        'subject' => $quiz->subject,
                        'class' => $quiz->class,
                        'attachment' => [
                            'date' => $attachment->date ?? now()->toDateString(),
                            'time' => $attachment->time ?? '00:30:00',
                            'number' => $attachment->number ?? 3,
                        ]
                    ],
                    'questions' => $questions,
                    'attempts' => [
                        'used' => $usedAttempts,
                        'remaining' => $maxAttempts - $usedAttempts,
                        'total' => $maxAttempts
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Quiz Start Error: ' . $e->getMessage(), [
                'quiz_id' => $quizId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submit(Request $request, $quizId)
    {
        try {
            $validated = $request->validate([
                'answers' => 'required|array',
                'answers.*' => 'required|integer|exists:option,id'
            ]);

            $user = Auth::user();
            $answers = $validated['answers'];

            $quiz = Quiz::findOrFail($quizId);

            // Urinishlar sonini tekshirish
            $attachment = DB::table('attachment')
                ->where('quiz_id', $quizId)
                ->first();

            $maxAttempts = $attachment->number ?? 3;
            $usedAttempts = DB::table('exam')
                ->where('user_id', $user->id)
                ->where('quiz_id', $quizId)
                ->count();

            if ($usedAttempts >= $maxAttempts) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimal urinishlar soniga yetgansiz. Quizni qayta topshira olmaysiz.'
                ], 403);
            }

            DB::beginTransaction();

            try {
                // To'g'ri javoblarni hisoblash
                $score = 0;
                $totalQuestions = count($answers);
                $detailedResults = [];

                foreach ($answers as $questionId => $selectedOptionId) {
                    $correctOption = DB::table('option')
                        ->where('question_id', $questionId)
                        ->where('is_correct', 1)
                        ->first();

                    $isCorrect = ($correctOption && $correctOption->id == $selectedOptionId);

                    if ($isCorrect) {
                        $score++;
                    }

                    $detailedResults[] = [
                        'question_id' => $questionId,
                        'selected_option_id' => $selectedOptionId,
                        'correct_option_id' => $correctOption ? $correctOption->id : null,
                        'is_correct' => $isCorrect
                    ];
                }

                // Exam jadvaliga saqlash
                $examId = DB::table('exam')->insertGetId([
                    'subject_id' => $quiz->subject_id,
                    'quiz_id' => $quizId,
                    'user_id' => $user->id,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // ExamAnswer jadvaliga javoblarni saqlash
                foreach ($answers as $questionId => $selectedOptionId) {
                    DB::table('exam_answer')->insert([
                        'exam_id' => $examId,
                        'question_id' => $questionId,
                        'option_id' => $selectedOptionId,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::commit();

                $percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100, 2) : 0;

                return response()->json([
                    'success' => true,
                    'message' => 'Quiz muvaffaqiyatli yakunlandi!',
                    'data' => [
                        'exam_id' => $examId,
                        'score' => $score,
                        'total_questions' => $totalQuestions,
                        'percentage' => $percentage,
                        'passed' => $percentage >= 70,
                        'detailed_results' => $detailedResults
                    ]
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Quiz Submit Error: ' . $e->getMessage(), [
                'quiz_id' => $quizId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz topilmadi.'
                ], 404);
            }
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Javoblar kiritishda xatolik. Iltimos, kiritilgan maʼlumotlarni tekshiring.',
                    'errors' => $e->errors()
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Kutilmagan tizim xatosi: ' . $e->getMessage()
            ], 500);
        }
    }
}
