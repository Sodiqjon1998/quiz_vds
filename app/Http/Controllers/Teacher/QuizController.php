<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher\Question;
use App\Models\Teacher\Quiz;
use App\Models\Teacher\Teacher;
use Auth;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Quiz::find()->paginate(20);
        return view('teacher.quiz.index', compact('model'));
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
        $model = new Quiz();
        $model->name = $request->input('name');
        $model->status = $request->input('status');
        $model->classes_id = $request->input('classes_id');
        $model->subject_id = Teacher::subject(Auth::user()->subject_id)->id;
        $model->created_by = Auth::user()->id;
        $model->updated_by = Auth::user()->id;
        if ($model->save()) {
            return redirect()->route('teacher.quiz.index');
        }
        return redirect()->route('teacher.quiz.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $model = Quiz::find()->findOrFail($id);
        $questions = Question::find()->where(['quiz_id' => $id])->paginate(20);
        return view('teacher.quiz.show', compact('model', 'questions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $model = Quiz::findOrFail($id);
        $questions = Question::find()->where(['quiz_id' => $id])->paginate(20);


        return view('teacher.quiz.edit', [
            'model' => $model,
            'questions' => $questions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'classes_id' => 'required|exists:classes,id',
        ]);

        $model = Quiz::findOrFail($id);
        $model->name = $request->input('name');
        $model->status = $request->input('status');
        $model->classes_id = $request->input('classes_id');
        $model->subject_id = $request->input('subject_id');
        $model->created_by = Auth::user()->id;
        $model->updated_by = Auth::user()->id;
        if ($model->save()) {
            return redirect()->route('teacher.quiz.index');
        }
        return redirect()->route('teacher.quiz.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $model = Quiz::findOrFail($id);
        $model->delete();
        return redirect()->route('teacher.quiz.index');
    }
}
