<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Subjects;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Subjects::where('status', Subjects::STATUS_ACTIVE)->paginate(20);

        return view('backend.subjects.index', compact('model'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $model = new Subjects();
        $model->name = $request->input('name');
        $model->status = $request->input('status');
        $model->created_by = \Auth::id();
        $model->updated_by = \Auth::id();
        if ($model->save()) {
            return redirect()->route('backend.subjects.index');

        } else {
            return redirect()->route('backend.subjects.create');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('backend.subjects.show', [
            'model' => Subjects::findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('backend.subjects.edit', [
            'model' => Subjects::findOrFail($id)
        ]);
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
        $model = Subjects::findOrFail($id);
        if ($model->delete()) {
            return redirect()->route('backend.subjects.index')->with('success', 'Fan o\'chirildi');
        } else {
            return redirect()->back()->with('error', 'Xatolik');
        }
    }
<<<<<<< HEAD

    public function test()
    {
        return view('backend.subjects.test');
    }
=======
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
}
