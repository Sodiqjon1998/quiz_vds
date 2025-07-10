<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Models\Teacher\Quiz;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Http\Controllers\Controller;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Quiz::find()->paginate(20);
        return view('teacher.exam.index', [
            'model' => $model
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
            'model' => $model
        ]);
    }

    public function showTest(string $id){
        $exam = Exam::findOrFail($id);
        $examAnswers = ExamAnswer::where('exam_id', '=', $id)->get();

        return view('teacher.exam.showTest', [
            'exam' => $exam,
            'examAnswers' => $examAnswers
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
