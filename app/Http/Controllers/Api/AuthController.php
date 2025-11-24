<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = Users::where('name', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Login yoki parol noto\'g\'ri!'
            ], 401);
        }

        if ($user->user_type != Users::TYPE_STUDENT) {
            return response()->json(['message' => 'Ruxsat berilmagan foydalanuvchi turi!'], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        // classes_id ni parse qilish (agar JSON bo'lsa)
        $classesId = $user->classes_id;

        // Agar classes_id JSON formatida bo'lsa
        if (is_string($classesId)) {
            try {
                $decoded = json_decode($classesId, true);
                if (is_numeric($decoded)) {
                    $classesId = $decoded;
                }
            } catch (\Exception $e) {
                \Log::error('classes_id parse error: ' . $e->getMessage());
            }
        }

        // Sinf ma'lumotini olish
        $classInfo = null;
        if ($classesId) {
            $class = \App\Models\Classes::find($classesId);
            if ($class) {
                $classInfo = [
                    'id' => $class->id,
                    'name' => $class->name,
                    'telegram_chat_id' => $class->telegram_chat_id ?? null,
                    'telegram_topic_id' => $class->telegram_topic_id ?? null
                ];
            }
        }

        // Debug log
        \Log::info('Login user classes_id:', ['raw' => $user->classes_id, 'parsed' => $classesId, 'class' => $classInfo]);

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'first_name' => $user->first_name ?? null,
                'last_name' => $user->last_name ?? null,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'user_type' => $user->user_type,
                'classes_id' => $classesId,
                'class' => $user->classRelation ? [ // ← classRelation ishlatish
                    'id' => $user->classRelation->id,
                    'name' => $user->classRelation->name,
                    'telegram_chat_id' => $user->classRelation->telegram_chat_id ?? null,
                    'telegram_topic_id' => $user->classRelation->telegram_topic_id ?? null
                ] : null,
                'status' => $user->status,
                'img' => $user->img ?? null
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout muvaffaqiyatli'
        ]);
    }

    // Profile ma'lumotlarini olish
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'first_name' => $user->first_name ?? null,
                'last_name' => $user->last_name ?? null,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'user_type' => $user->user_type,
                'classes_id' => $user->classes_id,
                'class' => $user->classRelation ? [ // ← classRelation ishlatish
                    'id' => $user->classRelation->id,
                    'name' => $user->classRelation->name,
                    'telegram_chat_id' => $user->classRelation->telegram_chat_id ?? null,
                    'telegram_topic_id' => $user->classRelation->telegram_topic_id ?? null
                ] : null,
                'status' => $user->status,
                'img' => $user->img ?? null
            ]
        ]);
    }



    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $user->id,
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        // Parol o'zgartirish
        if ($request->filled('new_password')) {
            if (!$request->filled('current_password')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Joriy parolni kiriting!'
                ], 422);
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Joriy parol noto\'g\'ri!'
                ], 422);
            }

            $user->password = Hash::make($request->new_password);
        }

        // Rasm yuklash
        if ($request->hasFile('img')) {
            // Eski rasmni o'chirish
            if ($user->img && Storage::disk('public')->exists($user->img)) {
                Storage::disk('public')->delete($user->img);
            }

            $path = $request->file('img')->store('avatars', 'public');
            $user->img = $path;
        }

        // Ma'lumotlarni yangilash
        $user->name = $validated['name'];
        $user->first_name = $validated['first_name'] ?? null;
        $user->last_name = $validated['last_name'] ?? null;
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil muvaffaqiyatli yangilandi!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'user_type' => $user->user_type,
                'classes_id' => $user->classes_id,
                'class' => $user->classRelation ? [ // ← classRelation ishlatish
                    'id' => $user->classRelation->id,
                    'name' => $user->classRelation->name,
                    'telegram_chat_id' => $user->classRelation->telegram_chat_id ?? null,
                    'telegram_topic_id' => $user->classRelation->telegram_topic_id ?? null
                ] : null,
                'status' => $user->status,
                'img' => $user->img
            ]
        ]);
    }
}
