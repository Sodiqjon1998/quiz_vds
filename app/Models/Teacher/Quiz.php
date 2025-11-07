<?php

namespace App\Models\Teacher;

use App\Models\Classes;
use App\Models\Option;
use App\Models\Subjects;
use App\Models\Users;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property int $subject_id
 * @property int $classes_id
 * @property int $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher\Question> $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz query()
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereClassesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Quiz extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;

    use HasFactory;

    public static function find()
    {
        return static::query()->where('created_by', '=', \Auth::user()->id);
    }

    protected $table = 'quiz';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'subject_id',
        'classes_id',
        'status',
        'created_by',
        'updated_by'
    ];

    public static function getStatus($id = null)
    {
        $status = [
            self::STATUS_ACTIVE => 'Faol',
            self::STATUS_IN_ACTIVE => "Bloklangan",
        ];

        return !is_null($id) ? $status[$id] : $status;
    }

    public static function getClass($id = null)
    {
        $class = Classes::where('id', $id)->first();
        return !is_null($class) ? $class : null;
    }

    public static function getClassesList()
    {
        $classes = Classes::where('status', '=', Classes::STATUS_ACTIVE)->get();
        return !is_null($classes) ? $classes : null;
    }

    public static function getOptionById($id = null){
        $options = Option::where('question_id', '=', $id)->get();
        return !is_null($options) ? $options : null;
    }

    public static function getAttachmentById($id = null){
        $attachment = Attachment::find()->where('quiz_id', '=', $id)->first();
        return !is_null($attachment) ? $attachment : null;
    }


    // Relations
    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'classes_id');
    }

    public function creator()
    {
        return $this->belongsTo(Users::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Users::class, 'updated_by');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_id');
    }

    public function attachment()
    {
        return $this->hasMany(Attachment::class, 'quiz_id');
    }

    public function attachmentOne()
    {
        // Agar har bir Quizga bitta 'attachment' (sozlama) mos kelsa:
        return $this->hasOne(Attachment::class, 'quiz_id');
        // Yoki Attachment modelini to'g'ri import qilish kerak
    }

    /**
     * Scope - faqat faol quizlar
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
