<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttemptState;
use App\Models\Question;
use App\Models\QuizTime;
use App\Models\Student\Quiz;
use App\Models\Subjects;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = DB::table('quiz')
            ->select([
                'quiz.name as quizName',
                'quiz.id as quizId',
                'quiz.subject_id as quizSubjectId',
                'quiz.classes_id as quizClassesId',
                'quiz.status as quizStatus',
                'subjects.id as subjectId',
                'subjects.name as subjectName',
                'classes.id as classesId',
                'classes.name as classesName',
                'attachment.date as date',   // <-- MUHIM: attachments.date ustunini tanlab oling
                'attachment.number as number', // <-- MUHIM: attachments.number ustunini tanlab oling
                'attachment.time as time',
            ])
            ->leftJoin('subjects', 'subjects.id', '=', 'quiz.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'quiz.classes_id')
            //            ->leftJoin('users', 'users.id', '=', 'classes.id')
            ->where('classes.id', '=', \Auth::user()->classes_id)
            ->leftJoin('attachment', 'attachment.quiz_id', '=', 'quiz.id')
            ->paginate(20);
        return view('student.quiz.index', compact('model'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teacher.quiz.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $quizId = $request->input('quizId');
        $subjectId = $request->input('subjectId');
        $questionIds = $request->input('question'); // Javob berilgan savollar ID'lari
        $optionIds = $request->input('option');     // Tanlangan variantlar ID'lari
        $clearState = $request->input('clearState', false); // Frontenddan holatni tozalash so'rovi

        // Exam yozuvini yaratish
        $exam = Exam::create([
            'quiz_id' => $quizId,
            'user_id' => $user->id,
            'subject_id' => $subjectId,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $correctCount = 0;
        $wrongCount = 0;

        // Har bir javobni saqlash va to'g'riligini tekshirish
        if ($questionIds && is_array($questionIds) && $optionIds && is_array($optionIds)) {
            foreach ($questionIds as $index => $questionId) {
                $selectedOptionId = $optionIds[$index] ?? null;

                if ($selectedOptionId) {
                    $question = Question::with('options')->find($questionId);
                    if ($question) {
                        $isCorrect = false;
                        foreach ($question->options as $option) {
                            if ($option->id == $selectedOptionId && $option->is_correct) {
                                $isCorrect = true;
                                break;
                            }
                        }

                        ExamAnswer::create([
                            'exam_id' => $exam->id,
                            'question_id' => $questionId,
                            'option_id' => $selectedOptionId,
                            'created_by' => $user->id,
                            'updated_by' => $user->id,
                        ]);

                        if ($isCorrect) {
                            $correctCount++;
                        } else {
                            $wrongCount++;
                        }
                    }
                }
            }
        }

        // Exam natijalarini yangilash
        $exam->save();

        // Agar test muvaffaqiyatli yakunlansa va holatni tozalash so'ralsa
        if ($clearState) {
            // O'chirishdan oldin mavjud holatni tekshirish
            $attemptState = ExamAttemptState::where('user_id', $user->id)
                ->where('quiz_id', $quizId)
                ->first();

            if ($attemptState) {
                \Log::info("Found ExamAttemptState to delete: ID " . $attemptState->id);
                $deletedCount = ExamAttemptState::where('user_id', $user->id)
                    ->where('quiz_id', $quizId)
                    ->delete();
                \Log::info("Deleted " . $deletedCount . " ExamAttemptState records for User ID " . $user->id . " and Quiz ID " . $quizId);
            } else {
                \Log::info("No ExamAttemptState found to delete for User ID " . $user->id . " and Quiz ID " . $quizId);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Test muvaffaqiyatli yakunlandi.', 'examId' => $exam->id]);
    }



    /**
     * Display the specified resource.
     */

    public function show(string $id, string $subjectId)
    {
        $subject = Subjects::findOrFail($subjectId);
        $quiz = Quiz::with('attachment')->findOrFail($id);
        $examAttachmentCount = Exam::where('quiz_id', $id)
            ->where('user_id', \Auth::user()->id)
            ->where('subject_id', $subjectId)
            ->count();

        $attachment = Attachment::getAttamptById($id);

        if (!$attachment) {
            return view('student.quiz.error', [
                'message' => "Imtihon ma'lumoti topilmadi.",
            ]);
        }
        //
        if ($attachment->number <= $examAttachmentCount) {
            return view('student.quiz.error', [
                'message' => "Urunishlar qolmadi",
                'date' => $attachment->date
            ]);
        }

        $examDate = Carbon::parse($attachment->date);
        $today = Carbon::today();
        //
        if ($examDate->isToday()) {
            $questions = Question::where('quiz_id', '=', $id)
                ->where('status', '=', Question::STATUS_ACTIVE)
                ->with('options')
                ->get();

            return view('student.quiz.show', [
                'questions' => $questions,
                'quiz' => $quiz,
                'subject' => $subject
            ]);
        } else if ($examDate->isFuture()) {
            return view('student.quiz.error', [
                'message' => "Qo'yilgan imtihon vaqti kelmadi",
                'date' => $attachment->date
            ]);
        } else {
            return view('student.quiz.error', [
                'message' => "Qo'yilgan imtihon vaqti tugadi!",
                'date' => $attachment->date
            ]);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function result(string $id)
    {
        $exam = Exam::findOrFail($id);
        $examAnswers = ExamAnswer::where('exam_id', '=', $id)->get();

        return view('student.quiz.result', [
            'exam' => $exam,
            'examAnswers' => $examAnswers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}


    // app/Http/Controllers/Student/QuizController.php
    public function saveAttemptState(Request $request)
    {
        $user = Auth::user();
        $quizId = $request->input('quizId');
        // $currentQuestionIndex = $request->input('currentQuestionIndex');
        // $remainingTime = $request->input('remainingTime');
        // $userAnswers = $request->input('userAnswers', []); // JavaScriptdan JSON string keladi
        // $questionStatuses = $request->input('questionStatuses', []); // JavaScriptdan JSON string keladi

        $examAttemptState = ExamAttemptState::updateOrCreate(
            [
                'user_id' => $user->id,
                'quiz_id' => $quizId,
            ],
            [
                'current_question_index' => $request->input('currentQuestionIndex'),
                'remaining_time' => $request->input('remainingTime'),
                'user_answers' => json_encode($request->input('userAnswers')),
                'question_statuses' => json_encode($request->input('questionStatuses')),
                'updated_at' => now(),
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Holat saqlandi.']);
    }


    // app/Http/Controllers/Student/QuizController.php
    public function getAttemptState($quizId)
    {
        $user = Auth::user();
        $attemptState = ExamAttemptState::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->first();

        if ($attemptState) {
            return response()->json([
                'status' => 'success',
                'currentQuestionIndex' => $attemptState->current_question_index,
                'remainingTime' => $attemptState->remaining_time,
                // Agar modelda $casts bor bo'lsa, bu yerda json_decode() kerak emas!
                'userAnswers' => $attemptState->user_answers, // To'g'ridan-to'g'ri beramiz
                'questionStatuses' => $attemptState->question_statuses, // To'g'ridan-to'g'ri beramiz
            ]);
        }

        return response()->json(['status' => 'not_found', 'message' => 'Saqlangan holat topilmadi.']);
    }

    public function clearAttemptState(Request $request)
    {
        $user = Auth::user();
        $quizId = $request->input('quizId');
        $clearState = $request->input('clearState', false);

        // Foydalanuvchi va test uchun holatni toping va o'chiring
        if ($clearState) {
            ExamAttemptState::where('user_id', $user->id)
                ->where('quiz_id', $quizId)
                ->delete();
            return response()->json(['status' => 'success', 'message' => 'Holat tozalandi.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Holat tozalash so\'rovi qabul qilinmadi.']);
    }
}
