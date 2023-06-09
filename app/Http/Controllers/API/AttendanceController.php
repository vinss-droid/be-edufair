<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCode;
use App\Models\Questionnaire;
use App\Models\RegisterWebinar;
use App\Models\RegisterWorkshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth.apikey','auth:sanctum']);
    }

    public function attendance(Request $request)
    {

        DB::beginTransaction();

        try {

            $validation = Validator::make($request->all(), [
                'attendance_code' => 'required',
                'proof_attendance' => 'required',
                'questionnaires' => 'required|array',
            ]);

            if ($validation->fails())
                return response()->json(['errors' => $validation->errors()], 400);

            if ($this->participantHasAbsent($request->attendance_code))
                return response()->json(['message' => 'participants has been absent'], 202);

            if ($this->checkAttendanceCode($request->attendance_code)) {

                if (!$this->checkUserIsRegister($request->attendance_code))
                    return response()->json(['message' => 'participants are not registered for this event'], 403);

//                return $request->questionnaires;

                Attendance::create([
                    'user_id' => Auth::user()->id,
                    'attendance_code' => $request->attendance_code,
                    'proof_attendance' => $this->publicLink() . '/' . $request->proof_attendance
                ]);

                foreach ($request->questionnaires as $questionnaire) {

                    Questionnaire::create([
                        'user_id' => Auth::user()->id,
                        'attendance_code' => $request->attendance_code,
                        'question' => $questionnaire['question'],
                        'answer' => $questionnaire['answer']
                    ]);

                }

                DB::commit();

                return response()->json(['message' => 'successful attendance'], 200);

            } else {
                return response()->json(['message' => 'missing attendance code'], 406);
            }

        } catch (\Exception) {
            DB::rollBack();
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }

    public function checkUserIsRegister($code)
    {

        $codes = AttendanceCode::where('code', $code)->first();

        $check = $codes->events === 'webinar' ?
            RegisterWebinar::where(['user_id' => Auth::user()->id, 'year' => $codes->year])->count() :
            RegisterWorkshop::where(['user_id' => Auth::user()->id, 'year' => $codes->year])->count();

        return $check > 0 ? true : false;

    }

    public function checkParticipantHasAbsent($code)
    {
        $check = Attendance::where(['user_id' => Auth::user()->id, 'attendance_code' => $code])->count();
        $checkCode = AttendanceCode::where(['code' => $code])->count();

        if ($checkCode <= 0) {
            return response()->json(['message' => 'missing attendance code'], 406);
        }

        return response()->json(['is_absent' => $check > 0 ? true : false], 200);
    }

    public function participantHasAbsent($code)
    {
        $check = Attendance::where(['user_id' => Auth::user()->id, 'attendance_code' => $code])->count();

        return $check > 0 ? true : false;
    }

    public function checkAttendanceCode($code)
    {

        try {
            $is_code = AttendanceCode::where('code', $code)->count();

            return $is_code > 0 ? true : false;

        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }

    public function publicLink()
    {
//        For Production
//        return asset('/storage/app/public/proof_file' . '/' . Auth::user()->id);
//      For Development
        return asset('/storage/proof_file' . '/' . Auth::user()->id);
    }

}
