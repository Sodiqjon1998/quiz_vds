<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $phone
 * @property int $user_type
 * @property int $status
 * @property string|null $img
 * @property int|null $classes_id
 * @property int|null $subject_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Users newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Users newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Users query()
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereClassesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Users whereUserType($value)
 * @mixin \Eloquent
 */
class Users extends Authenticatable
{
    const TYPE_ADMIN = 1;
    const TYPE_TEACHER = 2;
    const TYPE_KOORDINATOR = 3;
    const TYPE_STUDENT = 4;

    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;

    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'first_name',
        'last_name',
        'status',
        'classes_id',
        'phone',
        'subject_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'user_type'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'classes_id' => 'array',
    ];

    public static function getTypes($id = null)
    {
        $types = [
            self::TYPE_ADMIN => 'Admin',
            self::TYPE_TEACHER => "O'qituvchi",
            self::TYPE_KOORDINATOR => "Kordinator",
            self::TYPE_STUDENT => "O'quvchi",
        ];

        return !is_null($id) ? $types[$id] : $types;
    }

    public static function getStatus($id = null)
    {
        $status = [
            self::STATUS_ACTIVE => 'Faol',
            self::STATUS_IN_ACTIVE => "Bloklangan",
        ];

        return !is_null($id) ? $status[$id] : $status;
    }


    public static function getClassesList()
    {
        $classes = Classes::where('status', self::STATUS_ACTIVE)->get();
        return $classes;
    }

    public static function getClassesById($id)
    {
        $class = Classes::where('id', $id)->first();
        return $class;
    }

    public static function getSubjectsList()
    {
        $model = Subjects::where('status', Subjects::STATUS_ACTIVE)->get();

        return $model;
    }

    public static function getSubjectsById($id)
    {
        $model = Subjects::where('id', $id)->first();
        return $model;
    }

    public static function getStudentFullNameById($id)
    {
        $model = self::where('id', $id)->first();
        return $model->first_name . ' ' . $model->last_name;
    }


    public static function getByUserClassId($user_id)
    {
        $user = self::findOrFail($user_id);
        $class = Classes::findOrFail($user->classes_id);

        return $class;
    }

    public function subject(): BelongsTo // <--- Shuning uchun yuqorida use Illuminate\Database\Eloquent\Relations\BelongsTo qolishi kerak
    {
        // Subject modelini to'g'ri ko'rsatish
        return $this->belongsTo(Subjects::class, 'subject_id', 'id');
    }


    // app/Models/Users.php

    public function classRelation()
    {
        return $this->belongsTo(Classes::class, 'classes_id');
    }


    /**
     * O'sha oydagi yozuvlar soni
     */
    public function monthlyReadingsCount($month = null, $year = null): int
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return $this->readingRecords()
            ->month($month, $year)
            ->active()
            ->count();
    }

    /**
     * Jami o'qish vaqti (sekundlarda)
     */
    public function totalReadingTime(): int
    {
        return $this->readingRecords()
            ->active()
            ->sum('duration');
    }

    /**
     * Oxirgi 7 kunlik statistika
     */
    public function weeklyReadingStats(): array
    {
        $records = $this->readingRecords()
            ->where('created_at', '>=', now()->subDays(7))
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'count' => $records->count(),
            'total_duration' => $records->sum('duration'),
            'average_duration' => $records->avg('duration') ?? 0,
        ];
    }

    /**
     * Eng ko'p o'qigan oyi
     */
    public function bestMonth(): ?array
    {
        $monthlyStats = $this->readingRecords()
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->active()
            ->groupBy('year', 'month')
            ->orderBy('count', 'desc')
            ->first();

        if (!$monthlyStats) {
            return null;
        }

        return [
            'year' => $monthlyStats->year,
            'month' => $monthlyStats->month,
            'count' => $monthlyStats->count,
        ];
    }

    /**
     * O'qish streak'i (ketma-ket kunlar)
     */
    public function readingStreak(): int
    {
        $streak = 0;
        $currentDate = now()->startOfDay();

        while (true) {
            $hasReading = $this->readingRecords()
                ->whereDate('created_at', $currentDate)
                ->active()
                ->exists();

            if (!$hasReading) {
                break;
            }

            $streak++;
            $currentDate->subDay();
        }

        return $streak;
    }

    /**
     * Studentning barcha reportlari
     */
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class, 'student_id');
    }

    /**
     * Bugungi reportni olish
     */
    public function todayReport()
    {
        return $this->dailyReports()
            ->whereDate('report_date', today())
            ->with('taskCompletions')
            ->first();
    }

    /**
     * Ma'lum sananing reportini olish
     */
    public function getReportByDate($date)
    {
        return $this->dailyReports()
            ->whereDate('report_date', $date)
            ->with('taskCompletions')
            ->first();
    }

    /**
     * Oylik statistika
     */
    public function getMonthlyStats($year, $month)
    {
        return $this->dailyReports()
            ->whereYear('report_date', $year)
            ->whereMonth('report_date', $month)
            ->pluck('report_date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();
    }

    /**
     * Foydalanuvchining barcha kitobxonlik yozuvlari
     */
    public function readingRecords()
    {
        return $this->hasMany(ReadingRecord::class, 'users_id');
    }

    /**
     * Bugun audio yuklangan bormi?
     */
    public function hasTodayReading()
    {
        return $this->readingRecords()
            ->whereDate('created_at', today())
            ->where('status', ReadingRecord::STATUS_ACTIVE)
            ->exists();
    }

    /**
     * Oxirgi yozuvni olish
     */
    public function latestReading()
    {
        return $this->readingRecords()
            ->where('status', ReadingRecord::STATUS_ACTIVE)
            ->latest()
            ->first();
    }


    // 2. Sinf nomini olib beradigan yordamchi funksiya
    public function getClassNameAttribute()
    {
        // 1. Ma'lumotni olamiz
        $ids = $this->classes_id;

        // 2. Agar bo'sh bo'lsa, srazi qaytaramiz
        if (empty($ids)) {
            return '-';
        }

        // 3. MUHIM JOYI: Agar array bo'lmasa (string kelsa), uni arrayga o'rab olamiz
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        // 4. Endi bemalol whereIn ishlataveramiz (chunki $ids aniq array bo'ldi)
        $class = \Illuminate\Support\Facades\DB::table('classes')
            ->whereIn('id', $ids)
            ->first();

        return $class ? $class->name : '-';
    }
}
