<?php

namespace App\Models\Teacher;

use App\Models\Classes;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Subjects;
use App\Models\Users;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
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
 * @property-read \App\Models\Teacher\Attachment|null $attachment
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz query()
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz active()
 * @mixin \Eloquent
 */
class Quiz extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;

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

    protected $casts = [
        'status' => 'integer',
        'subject_id' => 'integer',
        'classes_id' => 'integer',
    ];

    // ======================
    // === STATIC METHODS ===
    // ======================

    public static function find()
    {
        return static::query()->where('created_by', '=', \Auth::user()->id);
    }

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

    public static function getOptionById($id = null)
    {
        $options = Option::where('question_id', '=', $id)->get();
        return !is_null($options) ? $options : null;
    }

    public static function getAttachmentById($id = null)
    {
        $attachment = Attachment::where('quiz_id', '=', $id)->first();
        return !is_null($attachment) ? $attachment : null;
    }

    // ======================
    // === RELATIONSHIPS ===
    // ======================

    /**
     * Quiz fan bilan bog'lanish
     */
    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    /**
     * Quiz sinf bilan bog'lanish
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'classes_id');
    }

    /**
     * Quiz yaratuvchi foydalanuvchi
     */
    public function creator()
    {
        return $this->belongsTo(Users::class, 'created_by');
    }

    /**
     * Quiz yangilovchi foydalanuvchi
     */
    public function updater()
    {
        return $this->belongsTo(Users::class, 'updated_by');
    }

    /**
     * Quiz savollari (ko'p)
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_id');
    }

    /**
     * ✅ Quiz attachment (bitta) - ASOSIY METOD
     * Bu metodni API'da va Livewire'da ishlating
     */
    public function attachment()
    {
        return $this->hasOne(Attachment::class, 'quiz_id')
            ->select('id', 'quiz_id', 'date', 'time', 'number', 'status', 'created_by', 'updated_by');
    }

    /**
     * ✅ Barcha attachmentlar (agar kerak bo'lsa)
     * Nomini o'zgartirdim: attachments (ko'plik)
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'quiz_id');
    }

    // ======================
    // === SCOPES ===
    // ======================

    /**
     * Faqat faol quizlar
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Faqat joriy foydalanuvchi yaratgan quizlar
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwnedBy($query, $userId = null)
    {
        $userId = $userId ?? \Auth::id();
        return $query->where('created_by', $userId);
    }

    /**
     * Ma'lum sinf uchun quizlar
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $classId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('classes_id', $classId);
    }

    /**
     * Ma'lum fan uchun quizlar
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $subjectId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }


    // Quiz.php modelga
    public function exams()
    {
        return $this->hasMany(Exam::class, 'quiz_id');
    }
}
