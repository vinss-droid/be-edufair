<?php

namespace App\Http\Controllers\API\Silogy;

use App\Http\Controllers\Controller;
use App\Models\RegisterSilogy;
use App\Models\SilogyTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth.apikey', 'auth:sanctum']);
    }

    public function check($year)
    {

        try {

            $check = RegisterSilogy::where(['user_id' => Auth::user()->id, 'year' => $year])->count();
            $data = RegisterSilogy::where(['user_id' => Auth::user()->id, 'year' => $year])->first();

            return response()->json([
                'is_registered' => $check > 0 ? true : false,
                'type' => $check ? $data->type : null
            ], 200);

        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request.'], 500);
        }

    }

    public function register(Request $request, $year)
    {

        DB::beginTransaction();

        try {

            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'team_lead' => 'required',
                'type' => 'required',
                'no_whatsapp' => 'required',
                'agency_name' => 'required',
                'province' => 'required',
                'regency' => 'required',
                'proof_himsika' => 'required',
                'proof_edufair' => 'required',
            ]);

            if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 400);

            if (RegisterSilogy::where(['user_id' => Auth::user()->id, 'year' => $year])->count() > 0)
                return response()->json(['message' => 'you have registered'], 406);

//            if (RegisterSilogy::where('year', $year)->count() > 100)
//                return response()->json(['message' => 'registration is full'], 406);

            $team = SilogyTeam::create([
               'team_lead' => ucwords(strtolower($request->team_lead)),
               'team_member' => json_encode($request->team_member),
            ]);

            RegisterSilogy::create([
                'user_id' => Auth::user()->id,
                'team_id' => $team->id,
                'type'=> $request->type,
                'year' => $year,
                'name' => ucwords(strtolower($request->name)),
                'email' => Auth::user()->email,
                'no_whatsapp' => $request->no_whatsapp,
                'agency_name' => $request->agency_name,
                'province' => $request->province,
                'regency' => $request->regency,
                'proof_himsika' => $this->publicLink() . '/' . $request->proof_himsika,
                'proof_edufair' => $this->publicLink() . '/' . $request->proof_edufair,
            ]);

            DB::commit();

            return response()->json(['message' => 'register success!'], 200);

        } catch (\Exception) {
            DB::rollBack();
            return response()->json(['message' => 'failed to processing request.'], 500);
        }

    }

    public function publicLink()
    {
//      For Production
//        return asset('/storage/app/public/proof_file' . '/' . Auth::user()->id);
//      For Development
        return asset('/storage/proof_file' . '/' . Auth::user()->id);
    }

    public function totalParticipant($year)
    {

        try {
            $participants = RegisterSilogy::where('year', $year)->count();
            return response()->json([
                'total_participant' => $participants
            ], 200);
        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request.'], 500);
        }

    }

}
