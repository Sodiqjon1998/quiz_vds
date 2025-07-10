<?php

namespace App\Models\Student;

use App\Models\Subjects;
use App\Models\User;

class Student extends User
{
    public static function findStudent()
    {
        return static::query()->where('user_type', '=', User::TYPE_STUDENT);
    }

    public static function getClassesById($id){
        $classId = Classes::where('id','=',$id)->first();

        return $classId;
    }
}
