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

            // DEBUG: URL to'g'ri shakllanayotganini tekshirish uchun log
            Log::info('--- Generating URLs for Index ---');
            
            $formattedRecordings = $recordings->map(function ($record) {
                // 1. Bazadagi "toza" qiymatni olamiz (Model accessorlaridan qochish uchun)
                // Bu getRawOriginal() funksiyasi modeldagi o'zgarishlarni chetlab o'tadi
                $rawFileUrl = $record->getRawOriginal('file_url');

                // 2. Agar bazaning o'zida to'liq URL (http...) yozilgan bo'lsa, o'shani o'zini olamiz
                if (str_starts_with($rawFileUrl, 'http')) {
                    $url = $rawFileUrl;
                } else {
                    // 3. Aks holda, storage linkini qo'shamiz (normal holat)
                    $url = asset('storage/' . $rawFileUrl);
                }

                if (app()->environment('production')) {
                    $url = str_replace('http://', 'https://', $url);
                }

                // Birinchi yozuvning URLini logga yozamiz (tekshirish uchun)
                Log::info("ID: {$record->id} | Generated URL: {$url}");

                return [
                    'id' => $record->id,
                    'book_name' => $record->book_name ?? $record->filename, 
                    'filename' => $record->filename, 
                    'file_url' => $record->file_url,
                    'audio_url' => $url, 
                    'duration' => $record->duration,
                    'file_size' => $record->file_size,
                    'created_at' => $record->created_at,
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
     * Audio fayl yuklash
     */
    public function upload(Request $request)
    {
        Log::info('--- Audio Upload Started ---');
        Log::info('User ID: ' . (Auth::id() ?? 'Guest'));

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

            if ($request->hasFile('audio')) {
                $file = $request->file('audio');
                Log::info('Audio File Info:', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size_bytes' => $file->getSize(),
                ]);
            }

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
            
            Log::info('Calculating audio duration...');
            $duration = $this->getAudioDuration($file);
            Log::info('Calculated Duration: ' . $duration . ' seconds');

            $fileName = time() . '_' . $user->id . '.' . $extension;
            $filePath = $file->storeAs('readings', $fileName, 'public');
            Log::info('File stored at: ' . $filePath);

            $fullPath = storage_path('app/public/' . $filePath);
            $compressedPath = $this->compressAudio($fullPath);

            if ($compressedPath) {
                $filePath = 'readings/' . basename($compressedPath);
                $fileSize = filesize($compressedPath);
                Log::info('File compressed. New size: ' . $fileSize);
            }

            $record = ReadingRecord::create([
                'users_id' => $user->id,
                'book_name' => $bookName,
                'filename' => $originalName, 
                'file_url' => $filePath,
                'file_size' => $fileSize,
                'duration' => $duration,
                'status' => ReadingRecord::STATUS_ACTIVE,
            ]);

            Log::info('Database record created with ID: ' . $record->id);

            return response()->json([
                'success' => true,
                'message' => 'Audio muvaffaqiyatli yuklandi!',
                'data' => [
                    'id' => $record->id,
                    'book_name' => $record->book_name,
                    'filename' => $record->filename,
                    'duration' => $record->duration,
                    'file_size' => $record->file_size,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Reading Upload Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
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
                Log::warning('FFMpeg class not found. Skipping compression.');
                return null;
            }

            $ffmpeg = FFMpeg::create();
            $audio = $ffmpeg->open($inputPath);

            $format = new Mp3();
            $format->setAudioKiloBitrate(32); 

            $outputPath = str_replace(['.webm', '.m4a', '.wav'], '_compressed.mp3', $inputPath);
            if ($outputPath == $inputPath) {
                $outputPath .= '.mp3';
            }
            
            $audio->save($format, $outputPath);

            if (file_exists($inputPath) && $inputPath !== $outputPath) {
                unlink($inputPath);
            }

            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Audio compression failed: ' . $e->getMessage());
            return null;
        }
    }

    private function getAudioDuration($file)
    {
        try {
            if (!class_exists('\getID3')) {
                Log::warning('getID3 library not found. Returning default duration.');
                return 180; 
            }

            $getID3 = new \getID3;
            $fileInfo = $getID3->analyze($file->getRealPath());

            if (isset($fileInfo['playtime_seconds'])) {
                return (int) $fileInfo['playtime_seconds'];
            }
            
            if (isset($fileInfo['error'])) {
                Log::warning('getID3 Error: ' . json_encode($fileInfo['error']));
            }

            return 180; 
        } catch (\Exception $e) {
            Log::error('getAudioDuration Exception: ' . $e->getMessage());
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