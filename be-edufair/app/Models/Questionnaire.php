<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionnaire extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [

        'question_id',
        'user_id',
        'attendance_code',
        'question',
        'answer',

    ];

    protected $hidden = ['created_at', 'updated_at'];

}
