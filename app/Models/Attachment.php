<?php

namespace App\Models;

use App\Models\Teacher\Quiz;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;

    use HasFactory;

    protected $table = 'attachment';

    public $timestamps = true;

    public static function getQuizId($id)
    {
        $quiz = Quiz::findOrFail($id);

        return $quiz;
    }


    public static function getStatus($id = null)
    {
        $status = [
            self::STATUS_ACTIVE => 'Faol',
            self::STATUS_IN_ACTIVE => "Bloklangan",
        ];

        return !is_null($id) ? $status[$id] : $status;
    }

    public static function getAttamptById($id = null){
        $model = self::where('quiz_id', $id)->first();
        return $model;
    }
}
