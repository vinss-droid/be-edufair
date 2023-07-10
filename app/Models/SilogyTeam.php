<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SilogyTeam extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'team_lead',
        'team_member'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $guarded = 'id';

}
