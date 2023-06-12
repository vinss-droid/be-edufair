<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{

    public function __construct()
    {

        $this->middleware(['auth:apikey', 'auth:sanctum']);

    }

    public function checkAttendanceCode($code)
    {

        try {

            $is_code = AttendanceCode::where('code', $code)->count();

            if ($is_code > 0) {

            } else {
                return response()->json(['message' => 'missing attendance code'], 406);
            }

        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }

}
