<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCode;
use App\Models\RegisterWebinar;
use App\Models\RegisterWorkshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

    public function participantAbsences($event, $year)
    {

        try {

            $attendances = DB::table('attendances')
                            ->join('register_webinars', 'attendances.user_id', '=', 'register_webinars.user_id')
                            ->join('attendance_codes', 'attendances.attendance_code', '=', 'attendance_codes.code')
                            ->where(['attendance_codes.events' => $event, 'register_webinars.year' => $year])
                            ->select('attendances.id',
                                'register_webinars.name',
                                'register_webinars.agency_name',
//                                'attendance_codes.events',
                                'attendances.proof_attendance'
                            )
                            ->paginate(20);

            return $attendances;

        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }
    public function participantWorkshops($year)
    {

        try {

            return RegisterWorkshop::paginate(20);

        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }

    public function participantWorkshopById($year, $id)
    {

        try {

            return response()->json([
                'participant' => RegisterWorkshop::participantByID($year, $id)
            ]);

        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }

    public function attendanceCode()
    {
        return AttendanceCode::paginate(15);
    }

    public function createAttendanceCode(Request $request, $year)
    {

        DB::beginTransaction();

        try {

            $validation = Validator::make($request->all(), [
               'events' => 'required'
            ]);

            if ($validation->fails())
                return response()->json(['errors' => $validation->errors()], 400);

            if (!$this->checkCodeForEvents($request->events, $year))
                return response()->json(['message' => "codes for this year's events are now available"], 406);

            AttendanceCode::create([
                'code' => strtoupper($this->generateRandomCode()),
                'year' => $year,
                'events' => $request->events,
                'is_active' => true
            ]);

            DB::commit();

            return response()->json(['message' => 'successfully create attendance code'], 201);

        } catch (\Exception) {
            DB::rollBack();
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }

    public function deleteAttendanceCode($id)
    {

        DB::beginTransaction();

        try {

            AttendanceCode::where('id', $id)->delete();

            DB::commit();
            return response()->json(['message' => 'successfully deleted'], 200);

        } catch (\Exception) {
            DB::rollBack();
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }

    public function changeIsActiveAttendanceCode(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $validation = Validator::make($request->all(), [
                'is_active' => 'required'
            ]);

            if ($validation->fails())
                return response()->json(['errors' => $validation->errors()], 400);

            AttendanceCode::where('id', $id)->update(['is_active' => $request->is_active]);

            DB::commit();
            return response()->json(['message' => 'successfully update is active attendance code'], 200);

        } catch (\Exception) {
            DB::rollBack();
            return response()->json(['message' => 'failed to processing request'], 500);
        }

    }

    public function checkCodeForEvents($event, $year)
    {
        try {

            $check = AttendanceCode::where(['events' => $event, 'year' => $year])->count();

            return $check > 0 ? false : true;

        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request'], 500);
        }
    }

    public function generateRandomCode()
    {
        return Str::random(6);
    }

}
