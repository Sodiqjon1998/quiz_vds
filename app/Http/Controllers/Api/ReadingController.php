<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReadingController extends Controller
{
    /**
     * Oylik kitobxonlik ma'lumotlarini olish
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $month = $request->input('month', date('n')); // 1-12
            $year = $request->input('year', date('Y'));

            // O'sha oydagi barcha yozuvlarni olish
            $recordings = ReadingRecord::where('users_id', $user->id)
                ->month($month, $year)
                ->active()
                ->orderBy('created_at', 'desc')
                ->get();

            // Statistika hisoblash
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $today = now();
            $isCurrentMonth = ($month == $today->month && $year == $today->year);
            
            // O'tgan kunlar soni
            $passedDays = $isCurrentMonth ? $today->day : $daysInMonth;
            
            // Yuklangan kunlar
            $completedDays = $recordings->count();
            
            // O'tkazilgan kunlar
            $missedDays = max(0, $passedDays - $completedDays - ($isCurrentMonth ? 1 : 0));

            // Jami davomiylik
            $totalDurationSeconds = $recordings->sum('duration');
            $totalDuration = gmdate('H:i:s', $totalDurationSeconds);

            // To'liqlik foizi
            $completionRate = $passedDays > 0 ? round(($completedDays / $passedDays) * 100, 2) : 0;

            // Bugun yuklangan bormi?
            $todayUploaded = $user->hasTodayReading();

            return response()->json([
                'success' => true,
                'data' => [
                    'recordings' => $recordings->map(function($record) {
                        return [
                            'id' => $record->id,
                            'filename' => $record->filename,
                            'file_url' => $record->file_url,
                            'duration' => $record->duration,
                            'file_size' => $record->file_size,
                            'created_at' => $record->created_at,
                        ];
                    }),
                    'statistics' => [
                        'completed_days' => $completedDays,
                        'missed_days' => $missedDays,
                        'total_duration' => $totalDuration,
                        'completion_rate' => $completionRate,
                        'today_uploaded' => $todayUploaded,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Reading Index Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Audio fayl yuklash
     */
   public function upload(Request $request)
{
    try {
        $user = Auth::user();
        
        // ✅ Foydalanuvchi autentifikatsiya qilinganligini tekshirish
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Autentifikatsiya xatosi. Qaytadan login qiling!'
            ], 401);
        }

        // Validatsiya
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a,webm|max:51200',
        ]);

        // Bugun allaqachon yuklangan bormi?
        if ($user->hasTodayReading()) {
            return response()->json([
                'success' => false,
                'message' => 'Siz bugun allaqachon audio yuklagansiz!'
            ], 403);
        }

        $file = $request->file('audio');
        $originalName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $extension = $file->getClientOriginalExtension();
        $duration = $this->getAudioDuration($file);

        // Faylni saqlash
        $fileName = time() . '_' . $user->id . '.' . $extension;
        $filePath = $file->storeAs('readings', $fileName, 'public');

        // ✅ users_id ni aniq belgilash
        $record = ReadingRecord::create([
            'users_id' => $user->id,  // ← Bu juda muhim!
            'filename' => $originalName,
            'file_url' => $filePath,
            'file_size' => $fileSize,
            'duration' => $duration,
            'status' => ReadingRecord::STATUS_ACTIVE,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Audio muvaffaqiyatli yuklandi!',
            'data' => [
                'id' => $record->id,
                'filename' => $record->filename,
                'duration' => $record->duration,
                'file_size' => $record->file_size,
            ]
        ], 200);

    } catch (\Exception $e) {
        Log::error('Reading Upload Error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Xatolik: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Audio davomiyligini aniqlash (getID3 bilan)
     */
    private function getAudioDuration($file)
    {
        try {
            // getID3 kutubxonasini tekshirish
            if (!class_exists('\getID3')) {
                return 180; // default 3 daqiqa
            }

            $getID3 = new \getID3;
            $fileInfo = $getID3->analyze($file->getRealPath());

            if (isset($fileInfo['playtime_seconds'])) {
                return (int) $fileInfo['playtime_seconds'];
            }

            return 180; // default 3 daqiqa
        } catch (\Exception $e) {
            Log::warning('Could not get audio duration: ' . $e->getMessage());
            return 180; // default 3 daqiqa
        }
    }

    /**
     * Yozuvni o'chirish
     */
    public function delete($id)
    {
        try {
            $user = Auth::user();

            $record = ReadingRecord::where('id', $id)
                ->where('users_id', $user->id)
                ->firstOrFail();

            // Model'dagi deleting event fayl o'chirishni avtomatik bajaradi
            $record->delete();

            return response()->json([
                'success' => true,
                'message' => 'Yozuv muvaffaqiyatli o\'chirildi'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Yozuv topilmadi'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Reading Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }
}