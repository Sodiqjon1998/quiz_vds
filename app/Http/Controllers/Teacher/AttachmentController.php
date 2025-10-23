<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Attachment::find()->paginate(20);
        return view('teacher.attachment.index', compact('model'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teacher.attachment.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $model = new Attachment();
        $model->number = $request->input('number');
        $model->quiz_id = $request->input('quiz_id');
        $model->date = $request->input('date');
        $model->time = $request->input('time');
        $model->status = $request->input('status');
        $model->created_by = \Auth::user()->id;
        $model->updated_by = \Auth::user()->id;
        if ($model->save()) {
            return redirect()->route('teacher.attachment.index');
        }
        return redirect()->route('teacher.attachment.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attachment $attachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $model = Attachment::findOrFail($id);
        return view('teacher.attachment.edit', compact('model'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $model = Attachment::findOrFail($id);
        $model->number = $request->input('number');
        $model->quiz_id = $request->input('quiz_id');
        $model->date = $request->input('date');
        $model->time = $request->input('time');
        $model->status = $request->input('status');
        $model->created_by = \Auth::user()->id;
        $model->updated_by = \Auth::user()->id;
        if ($model->save()) {
            return redirect()->route('teacher.attachment.index');
        }
        return redirect()->route('teacher.attachment.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $model = Attachment::findOrFail($id);
        $model->delete();
        return redirect()->route('teacher.attachment.index');
    }
}
