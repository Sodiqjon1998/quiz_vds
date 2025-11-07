<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// SiteController nomi o'zgarishsiz qoldi, QuizController ga o'zgartirish shart emas

class SiteController extends Controller
{

    public function index(Request $request)
    {
        // ⚠️ MUHIM: Urinishlar jadvalining taxminiy to'g'ri nomini e'lon qilamiz.
        // Agar sizning jadvalingiz nomi boshqacha bo'lsa, shu yerda o'zgartiring.
        $attemptsTableName = 'attachment'; // Sizning xato xabaringizda shu nom ko'rsatilgan.

        // --- 1. QUIZLAR RO'YXATINI OLISH QISMI ---
        $quizzes = DB::table('quiz')
            ->select([
                'quiz.id',
                'quiz.name as quiz_name',
                'quiz.status as quiz_status',
                'subjects.name as subject_name',
                'attachment.date',
                'attachment.time',
                'attachment.number as total_attempts',
            ])
            ->leftJoin('subjects', 'subjects.id', '=', 'quiz.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'quiz.classes_id')
            ->leftJoin('attachment', 'attachment.quiz_id', '=', 'quiz.id')
            ->where('classes.id', '=', Auth::user()->classes_id)
            ->paginate(20);

        // --- 2. JADVAL MAVJUDLIGINI TEKSHIRISH VA URINISHLARNI HISOBLASH ---

        // Bu tekshiruv 500 xatosini oldini oladi, agar jadval hali yaratilmagan bo'lsa.
        if (DB::getSchemaBuilder()->hasTable($attemptsTableName)) {
            $processedQuizzes = $quizzes->map(function ($quiz) use ($attemptsTableName) {

                // 🎯 Urinishlarni hisoblash. Xato xabariga ko'ra 'user_id' o'rniga 'created_by' ham tekshirilishi mumkin.
                // Biz hozircha 'user_id' ni saqlaymiz, agar u ishlamasa 'created_by' ga o'zgartirish kerak.
                $usedAttempts = DB::table($attemptsTableName)
                    ->where('created_by', Auth::id())
                    ->where('quiz_id', $quiz->id)
                    ->count();

                return [
                    'id' => $quiz->id,
                    'name' => $quiz->quiz_name,
                    'subject' => ['name' => $quiz->subject_name],
                    'date' => $quiz->date,
                    'time' => $quiz->time,
                    'status' => $quiz->quiz_status,
                    'attempts' => [
                        'used' => $usedAttempts,
                        'total' => $quiz->total_attempts ?? 1,
                    ],
                ];
            });

            // 🎯 Statistikani hisoblash. Completed uchun ham shu nom va 'created_by' ustuni ishlatiladi.
            $statistics = [
                'total' => DB::table('quiz')->where('classes_id', Auth::user()->classes_id)->count(),
                // Xato xabaridagi 'created_by = 40' ga asoslanib, 'created_by' ustunidan foydalanamiz
                'completed' => DB::table($attemptsTableName)
                    ->where('created_by', Auth::id())
                    ->distinct('quiz_id')
                    ->count(),
            ];

        } else {
            // Jadval topilmasa, urinishlarni va statistikani 0 ga tenglashtiramiz
            $processedQuizzes = $quizzes->map(function ($quiz) {
                return [
                    'id' => $quiz->id,
                    'name' => $quiz->quiz_name,
                    'subject' => ['name' => $quiz->subject_name],
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

        // --- 3. JAVOB QAYTARISH QISMI ---

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
}
