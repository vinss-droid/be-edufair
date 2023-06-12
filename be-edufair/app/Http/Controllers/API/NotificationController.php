<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\NotificationWebinarLinkMail;
use App\Models\RegisterWebinar;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth.apikey', 'auth:sanctum', 'auth.admin']);
    }

    public function webinarNotificationLink($year)
    {

        try {

            $participants = RegisterWebinar::participants($year);

            foreach ($participants as $participant) {
                Mail::to($participant->email)->send(new NotificationWebinarLinkMail());
            }

            return response()->json(['message' => 'Emails will be sent with a queue'], 200);

        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }

}
