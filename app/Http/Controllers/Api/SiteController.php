<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $attemptsTableName = 'attachment';

        $quizzes = DB::table('quiz')
            ->select([
                'quiz.id',
                'quiz.name as quiz_name',
                'quiz.status as quiz_status',
                'subjects.name as subject_name',
                'subjects.id as subject_id',
                'attachment.date',
                'attachment.time',
                'attachment.number as total_attempts',
            ])
            ->leftJoin('subjects', 'subjects.id', '=', 'quiz.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'quiz.classes_id')
            ->leftJoin('attachment', 'attachment.quiz_id', '=', 'quiz.id')
            ->where('classes.id', '=', Auth::user()->classes_id)
            ->paginate(20);

        if (DB::getSchemaBuilder()->hasTable($attemptsTableName)) {
            $processedQuizzes = $quizzes->map(function ($quiz) use ($attemptsTableName) {
                $usedAttempts = DB::table($attemptsTableName)
                    ->where('created_by', Auth::id())
                    ->where('quiz_id', $quiz->id)
                    ->count();

                return [
                    'id' => $quiz->id,
                    'name' => $quiz->quiz_name,
                    'subject' => [
                        'id' => $quiz->subject_id,
                        'name' => $quiz->subject_name
                    ],
                    'date' => $quiz->date,
                    'time' => $quiz->time,
                    'status' => $quiz->quiz_status,
                    'attempts' => [
                        'used' => $usedAttempts,
                        'total' => $quiz->total_attempts ?? 1,
                    ],
                ];
            });

            $statistics = [
                'total' => DB::table('quiz')->where('classes_id', Auth::user()->classes_id)->count(),
                'completed' => DB::table($attemptsTableName)
                    ->where('created_by', Auth::id())
                    ->distinct('quiz_id')
                    ->count(),
            ];
        } else {
            $processedQuizzes = $quizzes->map(function ($quiz) {
                return [
                    'id' => $quiz->id,
                    'name' => $quiz->quiz_name,
                    'subject' => [
                        'id' => $quiz->subject_id,
                        'name' => $quiz->subject_name
                    ],
                    'date' => $quiz->date,
                    'time' => $quiz->time,
                    'status' => $quiz->quiz_status,
                    'attempts' => [
                        'used' => 0,
                        'total' => $quiz->total_attempts ?? 1,
                    ],
                ];
            });

            $statistics = [
                'total' => DB::table('quiz')->where('classes_id', Auth::user()->classes_id)->count(),
                'completed' => 0,
            ];
        }

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
    }

    public function start(Request $request, $quizId)
    {
        $user = Auth::user();
        $quiz = Quiz::with(['subject', 'attachment'])->find($quizId);

        if (!$quiz) {
            return response()->json(['success' => false, 'message' => 'Quiz topilmadi.'], 404);
        }

        // Urinishlarni tekshirish
        $attemptsTableName = 'attachment';
        $maxAttempts = $quiz->attachment->number ?? 1;

        $usedAttempts = DB::table($attemptsTableName)
            ->where('created_by', $user->id)
            ->where('quiz_id', $quizId)
            ->count();

        if ($usedAttempts >= $maxAttempts) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal urinishlar sonidan foydalanilgan.'
            ], 403);
        }

        // Yangi urinishni yaratish
        try {
            $newAttempt = DB::table($attemptsTableName)->insertGetId([
                'quiz_id' => $quizId,
                'created_by' => $user->id,
                'started_at' => now(),
                'status' => 'IN_PROGRESS',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Urinishni yaratishda xato: ' . $e->getMessage(),
            ], 500);
        }

        // Savollarni olish
        $questions = DB::table('question')
            ->select('id', 'name', 'quiz_id')
            ->where('quiz_id', $quizId)
            ->inRandomOrder()
            ->get();

        // Variantlar bilan birlashtirish
        $questionsWithOptions = $questions->map(function ($question) {
            $options = DB::table('option')
                ->select('id', 'name')
                ->where('question_id', $question->id)
                ->get();

            $question->options = $options;
            return $question;
        });

        return response()->json([
            'success' => true,
            'message' => 'Quiz muvaffaqiyatli boshlandi.',
            'data' => [
                'attempt_id' => $newAttempt,
                'quiz_details' => [
                    'id' => $quiz->id,
                    'name' => $quiz->name,
                    'subject' => $quiz->subject,
                    'attachment' => $quiz->attachment
                ],
                'questions' => $questionsWithOptions
            ]
        ], 200);
    }

    public function submit(Request $request, $quizId)
    {
        $validated = $request->validate([
            'attempt_id' => 'required|integer',
            'answers' => 'required|array'
        ]);

        $user = Auth::user();
        $attemptId = $validated['attempt_id'];
        $answers = $validated['answers'];

        // Urinishni tekshirish
        $attempt = DB::table('attachment')
            ->where('id', $attemptId)
            ->where('quiz_id', $quizId)
            ->where('created_by', $user->id)
            ->first();

        if (!$attempt) {
            return response()->json([
                'success' => false,
                'message' => 'Urinish topilmadi yoki ruxsat yo\'q.'
            ], 404);
        }

        // To'g'ri javoblarni hisoblash
        $score = 0;
        $totalQuestions = 0;

        foreach ($answers as $questionId => $optionId) {
            $totalQuestions++;

            $correctOption = DB::table('option')
                ->where('id', $optionId)
                ->where('question_id', $questionId)
                ->where('is_correct', 1)
                ->exists();

            if ($correctOption) {
                $score++;
            }
        }

        // Urinishni yangilash
        DB::table('attachment')
            ->where('id', $attemptId)
            ->update([
                'status' => 'COMPLETED',
                'score' => $score,
                'total_questions' => $totalQuestions,
                'completed_at' => now(),
                'updated_at' => now(),
            ]);

        // Javoblarni saqlash (ixtiyoriy - agar kerak bo'lsa)
        // Bu uchun alohida 'quiz_answers' jadvali kerak bo'ladi

        return response()->json([
            'success' => true,
            'message' => 'Quiz muvaffaqiyatli topshirildi!',
            'data' => [
                'score' => $score,
                'total_questions' => $totalQuestions,
                'percentage' => round(($score / $totalQuestions) * 100, 2)
            ]
        ], 200);
    }
}
