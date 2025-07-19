<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException; // Va buni ham

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $diskSpace = $this->getDiskSpace();
        // Barcha sinflarni olish
        $allClasses = Classes::all();

        // Har bir oy bo'yicha sinflardagi o'quvchilar sonini hisoblash
        $studentsByClassAndMonth = [];

        // Eng kichik va eng katta yillarni aniqlash (grafik diapazoni uchun)
        $minYear = User::min('created_at') ? Carbon::parse(User::min('created_at'))->year : Carbon::now()->year - 5;
        $maxYear = Carbon::now()->year; // Faqat joriy yilgacha olamiz

        // Agar ma'lumotlar juda kam bo'lsa, defolt oralig'i
        if ($maxYear < $minYear) {
            $minYear = Carbon::now()->year - 5;
            $maxYear = Carbon::now()->year;
        }

        $currentMonth = Carbon::now()->month; // Joriy oy raqami

        // Ma'lumotlarni o'quvchilarning "created_at" sanasiga qarab yig'ish
        for ($year = $minYear; $year <= $maxYear; $year++) {
            // Agar joriy yil bo'lsa, faqat joriy oygacha hisoblaymiz
            $endMonth = ($year == Carbon::now()->year) ? $currentMonth : 12;

            for ($month = 1; $month <= $endMonth; $month++) {
                $monthKey = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT); // "YYYY-MM" format
                $studentsByClassAndMonth[$monthKey] = [];

                foreach ($allClasses as $class) {
                    $studentCount = User::where('classes_id', $class->id)
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->count();

                    $studentsByClassAndMonth[$monthKey][$class->name] = $studentCount;
                }
            }
        }

        return view('teacher.site.index', [
            'allClasses' => $allClasses,
            'studentsByClassAndMonth' => $studentsByClassAndMonth,
            'minYear' => $minYear,
            'maxYear' => $maxYear,
            'diskSpace' => $diskSpace
        ]);
    }



    private function getDiskSpace()
    {
        $process = Process::fromShellCommandline('df -h'); // df -h buyrug'ini bajaramiz
        $process->run();

        // Xatolik yuz bersa
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput(); // Buyruq natijasini olamiz

        // Natijani tahlil qilish
        $lines = explode("\n", $output);
        $diskInfo = [];

        // Odatda, sizning loyihangiz joylashgan "/" yoki "/var/www" bo'limini qidirasiz
        foreach ($lines as $line) {
            // Birinchi qator (header) yoki bo'sh qatorlarni o'tkazib yuboramiz
            if (str_starts_with($line, 'Filesystem') || empty(trim($line))) {
                continue;
            }

            // Bo'sh joylar bo'yicha ajratamiz va toza ma'lumotlarni olamiz
            $parts = preg_split('/\s+/', $line);

            // Sizga kerakli bo'limni aniqlang (masalan, root "/" yoki /var/www)
            // Bu yerda Mounted on ustuni asosida qidiramiz
            if (isset($parts[5]) && ($parts[5] === '/' || $parts[5] === '/var/www')) { // O'zgartirishingiz mumkin
                $diskInfo = [
                    'filesystem' => $parts[0],
                    'size'       => $parts[1],
                    'used'       => $parts[2],
                    'available'  => $parts[3],
                    'usage_percent' => $parts[4],
                    'mounted_on' => $parts[5],
                ];
                break; // Kerakli bo'limni topgach to'xtaymiz
            }
        }

        // Agar hech qanday ma'lumot topilmasa yoki xatolik bo'lsa
        if (empty($diskInfo)) {
            return [
                'available' => 'N/A',
                'usage_percent' => 'N/A',
                'error' => 'Disk maʼlumotlari topilmadi yoki xatolik yuz berdi.'
            ];
        }

        return $diskInfo;
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
