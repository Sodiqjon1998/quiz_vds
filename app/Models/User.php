<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $user_type
 * @property string|null $img
 * @property string|null $phone
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserType($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
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
        'status'
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

    public static function getSubjectsById($id){
        $model = Subjects::where('id', $id)->first();
        return $model;
    }

    public static function getStudentFullNameById($id){
        $model = self::where('id', $id)->first();
        return $model->first_name. ' '. $model->last_name;
    }


    public static function getByUserClassId($user_id){
        $user = self::findOrFail($user_id);
        $class = Classes::findOrFail($user->classes_id);

        return $class;
    }
}
