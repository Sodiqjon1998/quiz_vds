<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Users::whereIn('user_type', [Users::TYPE_TEACHER, Users::TYPE_KOORDINATOR])->paginate(20);
        return view('backend.users.index', [
            'model' => $model
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'subject_id' => 'nullable|exists:subjects,id',
            'status' => 'required|boolean'
        ]);

        // dd($request->switches_square_stacked_radio);

        $user = new Users();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->subject_id == 'null') {
            $user->subject_id = null;
        } else {
            $user->subject_id = $request->subject_id;
        }
        $user->password = Hash::make('12345678');
        $user->user_type = $request->user_type;
        $user->status = $request->status;

        if ($user->save()) {
            return redirect()->route('backend.users.index')->with('success', 'Ma\'lumotlar kiritildi');
        }
        return redirect()->back()->with('errors', 'Ma\lumotni saqlashda xatolik');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('backend.users.show', [
            'model' => Users::findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('backend.users.edit', [
            'model' => Users::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $model = Users::findOrFail($id);
        $model->status = $request->status;
        $model->subject_id = $request->subject_id;
        if ($model->save()) {
            return redirect()->route('backend.users.index')->with('success', 'Ma\'lumotlar kiritildi');
        }
        return redirect()->back()->with('error', 'Xatolik');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Users::findOrFail($id); // Foydalanuvchini topamiz
        $user->delete(); // Foydalanuvchini o‘chiramiz
        return redirect()->route('backend.users.index')->with('success', 'Foydalanuvchi muvaffaqiyatli o‘chirildi');
    }


    /**
     * Yangi o'quvchilar
     * //////////////
     */

    public function showStudent()
    {
        $users = Users::where(['status' => Users::STATUS_IN_ACTIVE, 'user_type' => Users::TYPE_STUDENT])->paginate(20);
        return view('backend.users.newstudent', [
            'model' => $users
        ]);
    }
}
