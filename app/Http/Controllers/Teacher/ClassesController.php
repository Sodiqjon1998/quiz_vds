<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\User;

class ClassesController extends Controller
{
    public function index()
    {
        $model = Classes::where('status', '=', Classes::STATUS_ACTIVE)->paginate(20);

        return view('teacher.classes.index', [
            'model' => $model
        ]);
    }

    
    public function show(string $id)
    {
        $model = User::where('classes_id', '=', $id)->paginate(20);

        return view('teacher.classes.show', [
            'model' => $model
        ]);
    }
}
