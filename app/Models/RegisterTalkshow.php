<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterTalkshow extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'type',
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

    public static function participantByID($year, $id)
    {

        $participant = RegisterWorkshop::where(['year' => $year, 'id' => $id])->first()->makeHidden(['user_id']);

        return $participant;

    }

}
