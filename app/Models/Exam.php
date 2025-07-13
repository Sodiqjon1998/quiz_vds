<?php

namespace App\Models;

use App\Models\Teacher\Quiz;
use App\Models\Teacher\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $table = 'exam';
    public $timestamps = true;

    // App/Models/Exam.php
    protected $fillable = [
        'subject_id',
        'quiz_id',
        'user_id',
        'created_by',
        'updated_by',
        // Boshqa barcha mass assignment qilinadigan ustunlarni shu yerga qo'shing
    ];

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }


    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

     public function user()
    {
        return $this->belongsTo(Teacher::class);
    }
}
