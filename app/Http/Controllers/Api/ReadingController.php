<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingRecord;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
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
            $month = $request->input('month', date('n'));
            $year = $request->input('year', date('Y'));

            $recordings = ReadingRecord::where('users_id', $user->id)
                ->month($month, $year)
                ->active()
                ->orderBy('created_at', 'desc')
                ->get();

            // Statistika hisoblash
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $today = now();
            $isCurrentMonth = ($month == $today->month && $year == $today->year);

            $passedDays = $isCurrentMonth ? $today->day : $daysInMonth;
            $completedDays = $recordings->count();
            $missedDays = max(0, $passedDays - $completedDays - ($isCurrentMonth ? 1 : 0));

            $totalDurationSeconds = $recordings->sum('duration');
            $totalDuration = gmdate('H:i:s', $totalDurationSeconds);

            $completionRate = $passedDays > 0 ? round(($completedDays / $passedDays) * 100, 2) : 0;
            $todayUploaded = $user->hasTodayReading();

            $totalSize = ReadingRecord::where('users_id', $user->id)
                ->where('status', ReadingRecord::STATUS_ACTIVE)
                ->sum('file_size');
            $totalSizeMB = round($totalSize / (1024 * 1024), 2);

            $formattedRecordings = $recordings->map(function ($record) {
                return [
                    'id' => $record->id,
                    'book_name' => $record->book_name ?? $record->filename,
                    'filename' => $record->filename,
                    'file_url' => $record->file_url,
                    'duration' => $record->duration,
                    'file_size' => round($record->file_size / 1024, 2) . ' KB',
                    'created_at' => $record->created_at->format('Y-m-d H:i'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'recordings' => $formattedRecordings,
                    'statistics' => [
                        'completed_days' => $completedDays,
                        'missed_days' => $missedDays,
                        'total_duration' => $totalDuration,
                        'completion_rate' => $completionRate,
                        'today_uploaded' => $todayUploaded,
                        'total_storage_used' => $totalSizeMB . ' MB',
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
     * Audio fayl yuklash - Backblaze B2 versiyasi (TO'G'RILANGAN)
     */
    public function upload(Request $request)
    {
        Log::info('--- Audio Upload Started (B2) ---');

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Autentifikatsiya xatosi.'
                ], 401);
            }

            $request->validate([
                'audio' => 'required|file|mimes:mp3,wav,ogg,m4a,webm|max:51200',
            ]);

            if ($user->hasTodayReading()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siz bugun allaqachon audio yuklagansiz!'
                ], 403);
            }

            $file = $request->file('audio');
            $originalName = $file->getClientOriginalName();
            $bookName = $request->input('book_name', $originalName);
            $fileSize = $file->getSize();
            $extension = $file->getClientOriginalExtension();

            $duration = $this->getAudioDuration($file);

            // 1. Temp folder ga saqlash
            $fileName = time() . '_' . $user->id . '.' . $extension;
            $localPath = $file->storeAs('temp_readings', $fileName, 'local');
            $fullPath = storage_path('app/' . $localPath);

            Log::info("Temp file created: " . $fullPath);

            // 2. Compression
            $compressedPath = $this->compressAudio($fullPath);

            if ($compressedPath && file_exists($compressedPath)) {
                $fileToUpload = $compressedPath;
                $fileSize = filesize($compressedPath);
                $finalFileName = basename($compressedPath);
                Log::info("Compressed file: " . $finalFileName . " (" . $fileSize . " bytes)");
            } else {
                $fileToUpload = $fullPath;
                $finalFileName = $fileName;
                Log::info("Using original file (compression skipped)");
            }

            // 3. B2 ga yuklash (TO'G'RILANGAN)
            $b2Path = 'readings/' . $finalFileName;

            try {
                // ✅ 1-usul: file_get_contents (oddiy)
                $fileContent = file_get_contents($fileToUpload);

                Log::info("Uploading to B2: " . $b2Path);

                $uploaded = Storage::disk('b2')->put($b2Path, $fileContent, 'public');

                // ✅ Tekshirish: Fayl haqiqatan ham yuklandi mi?
                if (!$uploaded) {
                    throw new \Exception('B2 ga yuklash muvaffaqiyatsiz tugadi (Storage::put qaytmadi)');
                }

                // ✅ Mavjudligini tekshirish
                if (!Storage::disk('b2')->exists($b2Path)) {
                    throw new \Exception('Fayl B2 da topilmadi: ' . $b2Path);
                }

                // ✅ To'liq URL yaratish
                $publicUrl = Storage::disk('b2')->url($b2Path);

                Log::info("✅ File successfully uploaded to B2: " . $publicUrl);
            } catch (\Exception $e) {
                Log::error('❌ B2 Upload Failed: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());

                // Temp fayllarni tozalash
                @unlink($fullPath);
                if ($compressedPath && $compressedPath !== $fullPath) {
                    @unlink($compressedPath);
                }

                throw new \Exception('Faylni B2 ga yuklashda xatolik: ' . $e->getMessage());
            }

            // 4. Temp fayllarni o'chirish
            @unlink($fullPath);
            if ($compressedPath && $compressedPath !== $fullPath) {
                @unlink($compressedPath);
            }

            // 5. Database ga saqlash
            $record = ReadingRecord::create([
                'users_id' => $user->id,
                'book_name' => $bookName,
                'filename' => $originalName,
                'file_url' => $publicUrl,  // To'liq B2 URL
                'file_size' => $fileSize,
                'duration' => $duration,
                'status' => ReadingRecord::STATUS_ACTIVE,
            ]);

            Log::info("✅ Record saved to database: ID " . $record->id);

            return response()->json([
                'success' => true,
                'message' => 'Audio muvaffaqiyatli yuklandi!',
                'data' => [
                    'id' => $record->id,
                    'book_name' => $record->book_name,
                    'filename' => $record->filename,
                    'file_url' => $record->file_url,
                    'duration' => $record->duration,
                    'file_size' => round($record->file_size / 1024, 2) . ' KB',
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('❌ Reading Upload Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    private function compressAudio($inputPath)
    {
        try {
            if (!class_exists('\FFMpeg\FFMpeg')) {
                Log::warning('FFMpeg not found. Skipping compression.');
                return null;
            }

            $ffmpeg = FFMpeg::create();
            $audio = $ffmpeg->open($inputPath);

            $format = new Mp3();
            $format->setAudioKiloBitrate(32);

            $outputPath = preg_replace('/\.(webm|m4a|wav|mp3)$/', '_compressed.mp3', $inputPath);

            $audio->save($format, $outputPath);

            Log::info("Audio compressed: " . $outputPath);

            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Compression failed: ' . $e->getMessage());
            return null;
        }
    }

    private function getAudioDuration($file)
    {
        try {
            if (!class_exists('\getID3')) {
                return 180;
            }

            $getID3 = new \getID3;
            $fileInfo = $getID3->analyze($file->getRealPath());

            return isset($fileInfo['playtime_seconds'])
                ? (int) $fileInfo['playtime_seconds']
                : 180;
        } catch (\Exception $e) {
            Log::error('Duration calculation failed: ' . $e->getMessage());
            return 180;
        }
    }

    public function delete($id)
    {
        try {
            $user = Auth::user();
            $record = ReadingRecord::where('id', $id)
                ->where('users_id', $user->id)
                ->firstOrFail();

            // Model booted() da avtomatik o'chiriladi
            $record->delete();

            return response()->json([
                'success' => true,
                'message' => 'Yozuv o\'chirildi'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }
}
