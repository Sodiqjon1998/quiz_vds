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
            Log::error('Reading index error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi'
            ], 500);
        }
    }

    /**
     * Audio yuklash - Private Bucket + Signed URL
     */
    public function upload(Request $request)
    {
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

            // 1. Temp file
            $fileName = time() . '_' . $user->id . '_' . uniqid() . '.' . $extension;
            $localPath = $file->storeAs('temp_readings', $fileName, 'local');
            $fullPath = storage_path('app/' . $localPath);

            if (!file_exists($fullPath)) {
                throw new \Exception('Temp fayl yaratilmadi');
            }

            // 2. Compression
            $compressedPath = $this->compressAudio($fullPath);

            if ($compressedPath && file_exists($compressedPath)) {
                $fileToUpload = $compressedPath;
                $fileSize = filesize($compressedPath);
                $finalFileName = basename($compressedPath);
            } else {
                $fileToUpload = $fullPath;
                $finalFileName = $fileName;
            }

            // 3. B2 Upload
            $b2Path = 'readings/' . $finalFileName;

            try {
                $fileContent = file_get_contents($fileToUpload);
                
                if ($fileContent === false) {
                    throw new \Exception('Faylni o\'qib bo\'lmadi');
                }

                $uploaded = Storage::disk('b2')->put($b2Path, $fileContent);

                if (!$uploaded) {
                    throw new \Exception('B2 upload failed');
                }

                if (!Storage::disk('b2')->exists($b2Path)) {
                    throw new \Exception('Fayl B2 da topilmadi');
                }

                // ✅ Signed URL (10 yillik)
                $signedUrl = Storage::disk('b2')->temporaryUrl(
                    $b2Path,
                    now()->addYears(10)
                );

            } catch (\Exception $e) {
                @unlink($fullPath);
                if ($compressedPath && $compressedPath !== $fullPath) {
                    @unlink($compressedPath);
                }

                Log::error('B2 upload failed', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Yuklashda xatolik',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            // 4. Cleanup
            @unlink($fullPath);
            if ($compressedPath && $compressedPath !== $fullPath) {
                @unlink($compressedPath);
            }

            // 5. Database
            $record = ReadingRecord::create([
                'users_id' => $user->id,
                'book_name' => $bookName,
                'filename' => $originalName,
                'file_url' => $signedUrl,  // ✅ Signed URL
                'file_size' => $fileSize,
                'duration' => $duration,
                'status' => ReadingRecord::STATUS_ACTIVE,
            ]);

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
            Log::error('Upload error', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Audio compression
     */
    private function compressAudio($inputPath)
    {
        try {
            if (!class_exists('\FFMpeg\FFMpeg')) {
                return null;
            }

            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/bin/ffprobe',
                'timeout' => 3600,
                'ffmpeg.threads' => 2,
            ]);

            $audio = $ffmpeg->open($inputPath);
            $format = new Mp3();
            $format->setAudioKiloBitrate(32);

            $outputPath = preg_replace('/\.(webm|m4a|wav|mp3|ogg)$/', '_compressed.mp3', $inputPath);
            $audio->save($format, $outputPath);

            return $outputPath;
        } catch (\Exception $e) {
            // Compression muvaffaqiyatsiz - original ishlatiladi
            return null;
        }
    }

    /**
     * Audio duration
     */
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
            return 180;
        }
    }

    /**
     * Delete
     */
    public function delete($id)
    {
        try {
            $user = Auth::user();
            $record = ReadingRecord::where('id', $id)
                ->where('users_id', $user->id)
                ->firstOrFail();

            // B2 dan o'chirish
            if ($record->file_url) {
                try {
                    // URL dan path extract qilish
                    $parsedUrl = parse_url($record->file_url);
                    $path = ltrim($parsedUrl['path'] ?? '', '/');
                    
                    // Query string (signed URL params) ni olib tashlash
                    $path = explode('?', $path)[0];
                    
                    if ($path && Storage::disk('b2')->exists($path)) {
                        Storage::disk('b2')->delete($path);
                    }
                } catch (\Exception $e) {
                    // B2 o'chirishda xatolik - davom etamiz
                }
            }

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

    /**
     * Yangi signed URL olish (agar eski expire bo'lsa)
     */
    public function refreshUrl($id)
    {
        try {
            $user = Auth::user();
            $record = ReadingRecord::where('id', $id)
                ->where('users_id', $user->id)
                ->firstOrFail();

            // File path ni extract qilish
            $parsedUrl = parse_url($record->file_url);
            $path = ltrim($parsedUrl['path'] ?? '', '/');
            $path = explode('?', $path)[0];

            if (!Storage::disk('b2')->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fayl topilmadi'
                ], 404);
            }

            // Yangi signed URL
            $newUrl = Storage::disk('b2')->temporaryUrl(
                $path,
                now()->addYears(10)
            );

            // Database update
            $record->update(['file_url' => $newUrl]);

            return response()->json([
                'success' => true,
                'data' => [
                    'file_url' => $newUrl
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }
}