<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;

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
                'class' => $classInfo, // Bu muhim!
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

        $classInfo = null;
        if ($user->classes_id) {
            $class = \App\Models\Classes::find($user->classes_id);
            if ($class) {
                $classInfo = [
                    'id' => $class->id,
                    'name' => $class->name,
                    'telegram_chat_id' => $class->telegram_chat_id ?? null,
                    'telegram_topic_id' => $class->telegram_topic_id ?? null
                ];
            }
        }

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
                'class' => $classInfo,
                'status' => $user->status,
                'img' => $user->img ?? null
            ]
        ]);
    }
}
