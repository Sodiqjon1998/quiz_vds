<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Classes::paginate(20);

        return view('backend.classes.index', compact('model'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.classes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $model = new Classes();

        $model->name = $request->input('name');
        $model->koordinator_id = $request->input('koordinator_id');
        $model->status = $request->input('status');
        $model->created_by = \Auth::user()->id;
        $model->updated_by = \Auth::user()->id;

        if ($model->save()) {
            return redirect()->route('backend.classes.index');
        }
        return redirect()->route('backend.classes.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $model = Classes::findOrFail($id);
        $students = User::where('classes_id', $id)->get();
        return view('backend.classes.show', [
            'model' => $model,
            'students' => $students
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $model = Classes::findOrFail($id);

        return view('backend.classes.edit', [
            'model' => $model
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $model = Classes::findOrFail($id);
        $model->name = $request->input('name');
        $model->koordinator_id = $request->input('koordinator_id');
        $model->status = $request->input('status');
        $model->created_by = \Auth::user()->id;
        $model->updated_by = \Auth::user()->id;
        if ($model->save()) {
            return redirect()->route('backend.classes.index');
        }
        return redirect()->route('backend.classes.edit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $model = Classes::findOrFail($id);
        $model->delete();
        return redirect()->route('backend.classes.index');
    }
}
