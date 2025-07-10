<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_IN_ACTIVE = 0;


    use HasFactory;

    protected $table = 'question';

    public $timestamps = true;


    public static function getStatus($id = null)
    {
        $status = [
            self::STATUS_ACTIVE => 'Faol',
            self::STATUS_IN_ACTIVE => "Bloklangan",
        ];

        return !is_null($id) ? $status[$id] : $status;
    }

    public static function getQuestionById($id){
        $model = Question::where('id', $id)->first();
        return $model;
    }


    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
