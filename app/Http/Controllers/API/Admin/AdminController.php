<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegisterWebinar;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth.apikey', 'auth:sanctum', 'auth.admin']);
    }

    public function participants($year)
    {

        try {

            return RegisterWebinar::paginate(20);

        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }

    public function participantById($year, $id)
    {

        try {

            return response()->json([
                'participant' => RegisterWebinar::participantByID($year, $id)
            ]);

        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }

}
