<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends \App\Models\Attachment
{
    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;

    use HasFactory;

    public static function find()
    {
        return static::query()->where('created_by', '=', \Auth::user()->id);
    }

    public static function getQuizList()
    {
        $quizList = Quiz::where('status', '=', Quiz::STATUS_ACTIVE)->get();
        return $quizList;
    }
}
