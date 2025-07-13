<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;
    protected $table = 'exam_answer';
    public $timestamps = true;

    // App/Models/ExamAnswer.php
    protected $fillable = [
        'exam_id',
        'question_id',
        'option_id',
        'created_by',
        'updated_by',
        // Boshqa barcha mass assignment qilinadigan ustunlarni shu yerga qo'shing
    ];


     public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
