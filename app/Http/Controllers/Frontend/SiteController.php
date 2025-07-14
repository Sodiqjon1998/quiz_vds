<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('frontend.site.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function register()
    {
        return view('frontend.site.register');
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
        // 1. Ma'lumotlarni validatsiya qilish
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'], // Telefon raqami noyob bo'lishi kerak
            'classes_id' => ['required', 'exists:classes,id'], // `classes` jadvalidagi `id` ustunida mavjud bo'lishi kerak
        ]);

        // 2. Yangi foydalanuvchi yaratish
        $user = new User();

        $user->name = $request->first_name;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->password = Hash::make('12345678');
        $user->phone = $request->phone;
        $user->status = User::STATUS_IN_ACTIVE;
        $user->user_type = User::TYPE_STUDENT;
        $user->classes_id = $request->classes_id;

        $user->save();

        // 3. Muvaqqat xabar yoki yo'naltirish
        // Ro'yxatdan o'tish muvaffaqiyatli bo'lsa, foydalanuvchini boshqa sahifaga yo'naltirish
        return redirect()->route('frontend.site.success', ['id' => $user->id])->with('success', "Ro'yxatdan o'tish muvaffaqiyatli yakunlandi!");
        // Yoki oldingi sahifaga qaytarish
        // return back()->with('success', "Ro'yxatdan o'tish muvaffaqiyatli yakunlandi!");
    }

    /**
     * Display the specified resource.
     */
    public function success(string $id)
    {
        $user = User::findOrFail($id);

        return view('frontend.site.success', [
            'user' => $user
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
