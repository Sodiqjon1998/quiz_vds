<?php


namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index() {
        return view('teacher.user.index');
    }

    public function setting()
    {
        return view('teacher.users.setting');
    }


    /**
     * Foydalanuvchi profil ma'lumotlarini yangilash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $userId = Auth::user()->id; // Avtorizatsiyadan o'tgan foydalanuvchi IDsi
        $user = Users::findOrFail($userId); // Foydalanuvchini bazadan topish

        // Validatsiya qoidalarini aniqlash
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            // Email noyob bo'lishi kerak, lekin joriy foydalanuvchining o'z emaili bundan mustasno
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ];

        // Agar yangi parol maydoni to'ldirilgan bo'lsa, parol validatsiyasini qo'shish
        if (!empty($request->input('new_password'))) {
            $rules['old_password'] = ['required', 'string']; // Eski parol kiritilgan bo'lishi shart
            $rules['new_password'] = ['required', 'string', 'min:8', 'confirmed']; // Yangi parol minimal 8 ta belgili va tasdiqlangan bo'lishi kerak
        }

        try {
            // Ma'lumotlarni validatsiya qilish
            $request->validate($rules);
        } catch (ValidationException $e) {
            // Validatsiya xatosi yuz berganda JSON formatida javob qaytarish
            return response()->json([
                'status' => 'error',
                'message' => 'Validatsiya xatosi.',
                'errors' => $e->errors(), // Validatsiya xatolarining batafsil ro'yxati
            ], 422); // 422 Unprocessable Entity HTTP status kodi
        }

        // Ism va E-pochta manzilini yangilash
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        // Parolni o'zgartirish logikasi
        if (!empty($request->input('new_password'))) {
            // Agar yangi parol kiritilgan bo'lsa, eski parolni tekshirish
            if (!Hash::check($request->input('old_password'), $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Joriy parol noto\'g\'ri.',
                ], 400); // 400 Bad Request HTTP status kodi
            }
            // Yangi parolni shifrlab saqlash
            $user->password = Hash::make($request->input('new_password'));
        }

        // Foydalanuvchi ma'lumotlarini bazaga saqlash
        $user->save();

        // Muvaffaqiyatli javob qaytarish
        return response()->json([
            'status' => 'success',
            'message' => 'Profil ma\'lumotlari muvaffaqiyatli yangilandi.',
        ]);
    }
}
