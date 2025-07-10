<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Teacher\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Question::find()->paginate(20);
        return view('teacher.question.index', compact('model'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teacher.question.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $model = new Question();
        $model->name = $request->input('name');
        $model->quiz_id = $request->input('quiz_id');
        $model->status = $request->input('status');
        $model->created_by = \Auth::user()->id;
        $model->updated_by = \Auth::user()->id;
        if ($model->save()) {
            foreach ($request->input('names') as $index => $name) {
                $option = new Option();

                $is_correct = ($request->input('is_correct') == $index) ? 1 : 0;
                $option->question_id = $model->id;
                $option->name = $name;
                $option->is_correct = $is_correct;
                $option->status = 1;
                $option->created_by = \Auth::user()->id;
                $option->updated_by = \Auth::user()->id;
                $option->save();
            }
            return redirect()->route('teacher.question.index')->with('success', 'Question has been created');
        }
        return redirect()->route('teacher.question.create')->with('error', 'Question could not be created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $model = Question::find()->findOrFail($id);
        $options = Option::where('question_id', $id)->get();
        return view('teacher.question.show', compact('model', 'options'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        //
    }
}
