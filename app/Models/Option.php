<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;
    protected $table = 'option';

    public static function getOptionById($id)
    {
        $model = self::where('id', $id)->first();
        return $model;
    }
}
