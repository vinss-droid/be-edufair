<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegisterWorkshop extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'year',
        'name',
        'email',
        'no_whatsapp',
        'agency_name',
        'province',
        'regency',
        'proof_himsika',
        'proof_edufair'
    ];

    protected $hidden = ['created_at', 'updated_at'];
    protected $guarded = 'id';

}
