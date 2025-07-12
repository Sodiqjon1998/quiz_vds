<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;

class SiteController extends Controller
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
            ->where('classes.id', '=', Auth::user()->classes_id)
            ->leftJoin('attachment', 'attachment.quiz_id', '=', 'quiz.id')
            ->paginate(20);
        return view('student.site.index', compact('model'));
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
    public function show(string $id)
    {
        //
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
