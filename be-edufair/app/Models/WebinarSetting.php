<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebinarSetting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'register_date',
        'implementation_date',
        'benefit_date',
        'webinar_group',
    ];

    protected $hidden = ['created_at', 'updated_at'];
    protected $guarded = 'id';

    public static function groupLink()
    {
        $checkSettings = WebinarSetting::count();
        $webinarSettings = WebinarSetting::first();

        return $checkSettings > 0 ? $webinarSettings->webinar_group : null;
    }

}
