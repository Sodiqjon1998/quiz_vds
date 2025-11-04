<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
}
