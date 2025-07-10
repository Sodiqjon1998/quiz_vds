<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;


    use HasFactory;

    protected $table = 'subjects';


    public static function getStatus($id = null)
    {
        $status = [
            self::STATUS_ACTIVE => 'Faol',
            self::STATUS_IN_ACTIVE => "Bloklangan",
        ];

        return !is_null($id) ? $status[$id] : $status;
    }

    public static function getTeacherById($id)
    {
        $teacher = User::where('subject_id', '=', $id)->get();
        return $teacher;
    }

    public static function getSubjectById($id){
        $model = Subjects::where('id', '=', $id)->first();
        return $model;
    }
}
