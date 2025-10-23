<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|QuizTime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuizTime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuizTime query()
 * @mixin \Eloquent
 */
class QuizTime extends Model
{
    use HasFactory;

    protected $table = 'quiz_time';
    protected $fillable = [
        'quiz_id',
        'user_id',
        'subject_id',
        'hours',
        'minutes',
        'seconds',
    ];

    public $timestamps = false;
}
