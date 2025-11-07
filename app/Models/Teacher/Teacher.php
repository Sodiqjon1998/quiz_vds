<?php

namespace App\Models\Teacher;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Classes;
use App\Models\Subjects;
use App\Models\Users;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereClassesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereUserType($value)
 * @mixin \Eloquent
 */
class Teacher extends Users
{
    public static function findTeacher()
    {
        return static::query()->where('user_type', '=', Users::TYPE_TEACHER);
    }

    // public function subject()
    // {
    //     return $this->belongsTo(Subjects::class, 'subject_id');
    // }

    public static function studentCount(string $id)
    {
        return self::where('classes_id', '=', $id)->count();
    }
}
