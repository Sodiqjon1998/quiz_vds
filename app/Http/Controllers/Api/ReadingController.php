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
     * Audio fayl yuklash - Backblaze B2 versiyasi
     */
    public function upload(Request $request)
    {
        Log::info('=== Audio Upload Started ===');

        try {
            $user = Auth::user();

            if (!$user) {
                Log::error('User not authenticated');
                return response()->json([
                    'success' => false,
                    'message' => 'Autentifikatsiya xatosi.'
                ], 401);
            }

            Log::info('User authenticated', ['user_id' => $user->id]);

            // Validatsiya
            $request->validate([
                'audio' => 'required|file|mimes:mp3,wav,ogg,m4a,webm|max:51200',
            ]);

            Log::info('Validation passed');

            // Bugun yuklanganini tekshirish
            if ($user->hasTodayReading()) {
                Log::warning('User already uploaded today', ['user_id' => $user->id]);
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

            Log::info('File info', [
                'original_name' => $originalName,
                'size' => $fileSize,
                'extension' => $extension
            ]);

            // Audio davomiyligini olish
            $duration = $this->getAudioDuration($file);
            Log::info('Audio duration: ' . $duration . ' seconds');

            // 1. Temp folder'ga saqlash
            $fileName = time() . '_' . $user->id . '.' . $extension;
            $localPath = $file->storeAs('temp_readings', $fileName, 'local');
            $fullPath = storage_path('app/' . $localPath);

            Log::info('Temp file created', ['path' => $fullPath]);

            if (!file_exists($fullPath)) {
                throw new \Exception('Temp fayl yaratilmadi: ' . $fullPath);
            }

            // 2. Compression (ixtiyoriy)
            $compressedPath = $this->compressAudio($fullPath);

            if ($compressedPath && file_exists($compressedPath)) {
                $fileToUpload = $compressedPath;
                $fileSize = filesize($compressedPath);
                $finalFileName = basename($compressedPath);
                Log::info('Using compressed file', [
                    'path' => $finalFileName,
                    'size' => $fileSize
                ]);
            } else {
                $fileToUpload = $fullPath;
                $finalFileName = $fileName;
                Log::info('Using original file (compression skipped)');
            }

            // 3. Backblaze B2 ga yuklash
            $b2Path = 'readings/' . $finalFileName;

            try {
                Log::info('Starting B2 upload', [
                    'local_path' => $fileToUpload,
                    'b2_path' => $b2Path,
                    'size' => $fileSize,
                    'file_exists' => file_exists($fileToUpload)
                ]);

                // Faylni o'qish
                $fileContent = file_get_contents($fileToUpload);
                
                if ($fileContent === false) {
                    throw new \Exception('Faylni o\'qib bo\'lmadi: ' . $fileToUpload);
                }

                Log::info('File content read', ['bytes' => strlen($fileContent)]);

                // B2 konfiguratsiyasini tekshirish
                Log::info('B2 Config', [
                    'bucket' => config('filesystems.disks.b2.bucket'),
                    'region' => config('filesystems.disks.b2.region'),
                    'endpoint' => config('filesystems.disks.b2.endpoint'),
                    'has_key' => !empty(config('filesystems.disks.b2.key')),
                    'has_secret' => !empty(config('filesystems.disks.b2.secret')),
                ]);

                // B2 ga yuklash
                $uploaded = Storage::disk('b2')->put($b2Path, $fileContent, [
                    'visibility' => 'public',
                    'CacheControl' => 'max-age=31536000',
                ]);

                if (!$uploaded) {
                    throw new \Exception('Storage::put() false qaytardi');
                }

                Log::info('Storage::put() successful');

                // Mavjudligini tekshirish
                $exists = Storage::disk('b2')->exists($b2Path);
                Log::info('File exists check', ['exists' => $exists]);

                if (!$exists) {
                    throw new \Exception('Fayl yuklandimi, lekin B2 da topilmadi: ' . $b2Path);
                }

                // URL olish
                $publicUrl = Storage::disk('b2')->url($b2Path);
                
                Log::info('✅ B2 Upload Successful', [
                    'path' => $b2Path,
                    'url' => $publicUrl
                ]);

            } catch (\Exception $e) {
                Log::error('❌ B2 Upload Failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Temp fayllarni tozalash
                @unlink($fullPath);
                if ($compressedPath && $compressedPath !== $fullPath) {
                    @unlink($compressedPath);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Faylni B2 ga yuklashda xatolik',
                    'error' => $e->getMessage(),
                    'debug' => [
                        'bucket' => config('filesystems.disks.b2.bucket'),
                        'region' => config('filesystems.disks.b2.region'),
                        'endpoint' => config('filesystems.disks.b2.endpoint'),
                        'path' => $b2Path
                    ]
                ], 500);
            }

            // 4. Temp fayllarni o'chirish
            @unlink($fullPath);
            if ($compressedPath && $compressedPath !== $fullPath) {
                @unlink($compressedPath);
            }

            Log::info('Temp files cleaned');

            // 5. Database ga saqlash
            $record = ReadingRecord::create([
                'users_id' => $user->id,
                'book_name' => $bookName,
                'filename' => $originalName,
                'file_url' => $publicUrl,
                'file_size' => $fileSize,
                'duration' => $duration,
                'status' => ReadingRecord::STATUS_ACTIVE,
            ]);

            Log::info('✅ Record saved to database', ['id' => $record->id]);

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
            Log::error('❌ Upload Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Audio ni siqish (compression)
     */
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

            Log::info('Audio compressed', ['output' => $outputPath]);

            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Compression failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Audio davomiyligini olish
     */
    private function getAudioDuration($file)
    {
        try {
            if (!class_exists('\getID3')) {
                Log::warning('getID3 not found. Using default duration.');
                return 180;
            }

            $getID3 = new \getID3;
            $fileInfo = $getID3->analyze($file->getRealPath());

            $duration = isset($fileInfo['playtime_seconds'])
                ? (int) $fileInfo['playtime_seconds']
                : 180;

            return $duration;
        } catch (\Exception $e) {
            Log::error('Duration calculation failed', ['error' => $e->getMessage()]);
            return 180;
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

            // B2 dan faylni o'chirish
            if ($record->file_url) {
                try {
                    // URL dan path ni olish
                    $path = parse_url($record->file_url, PHP_URL_PATH);
                    $path = ltrim($path, '/');
                    
                    if (Storage::disk('b2')->exists($path)) {
                        Storage::disk('b2')->delete($path);
                        Log::info('File deleted from B2', ['path' => $path]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to delete from B2', ['error' => $e->getMessage()]);
                }
            }

            // Database dan o'chirish
            $record->delete();

            return response()->json([
                'success' => true,
                'message' => 'Yozuv o\'chirildi'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Delete failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }
}
