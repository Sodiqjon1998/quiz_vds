<?php

// app/Http/Controllers/ChatController.php

namespace App\Http\Controllers\WebSocket;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Chat sahifasini ko'rsatish
     */
    public function index()
    {
        return view('teacher.chat.chat'); // yoki 'teacher.chat'
    }

    /**
     * Xabar yuborish
     */
    public function sendMessage(Request $request)
    {
        try {
            // Validatsiya
            $validated = $request->validate([
                'username' => 'required|string|max:255',
                'message' => 'required|string|max:1000',
            ]);

            Log::info('Xabar qabul qilindi:', $validated);

            // Eventni trigger qilish (broadcast)
            // ❗ MUHIM: toOthers() - o'zingizga qaytarmaslik uchun!
            broadcast(new MessageSent(
                $validated['username'],
                $validated['message']
            ))->toOthers();

            Log::info('Event broadcast qilindi (toOthers)');

            return response()->json([
                'success' => true,
                'message' => 'Xabar yuborildi',
                'data' => $validated
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validatsiya xatosi:', ['errors' => $e->errors()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validatsiya xatosi',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Xabar yuborishda xato:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server xatosi: ' . $e->getMessage()
            ], 500);
        }
    }
}