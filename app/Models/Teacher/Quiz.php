<?php

namespace App\Models\Teacher;

use App\Models\Classes;
use App\Models\Option;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
