<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Users::whereIn('user_type', [Users::TYPE_STUDENT])->paginate(20);

        return view('backend.student.index', compact('model'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.student.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $model = new Users();
        $model->first_name = $request->input('first_name');
        $model->last_name = $request->input('last_name');
        $model->name = $request->input('name');
        $model->email = $request->input('email');
        $model->phone = $request->input('phone');
        $model->user_type = Users::TYPE_STUDENT;
        $model->password = \Hash::make('12345678');
        $model->classes_id = $request->input('classes_id');

        if ($model->save()) {
            return redirect()->route('backend.student.index');
        }
        return redirect()->back()->with('error', 'Something went wrong');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('backend.student.show', [
            'model' => Users::findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $model = Users::findOrFail($id);

        return view('backend.student.edit', compact('model'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'classes_id' => 'required|exists:classes,id',
        ]);

        $model = Users::findOrFail($id);
        $model->first_name = $request->input('first_name');
        $model->last_name = $request->input('last_name');
        $model->name = $request->input('name');
        $model->email = $request->input('email');
        $model->phone = $request->input('phone');
        $model->classes_id = $request->input('classes_id');
        $model->status = $request->input('status');
        if ($model->save()) {
            return redirect()->route('backend.student.index');
        }
        return redirect()->back()->with('error', 'Something went wrong');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $model = Users::findOrFail($id);
        $model->delete();
        return redirect()->route('backend.student.index');
    }
}
