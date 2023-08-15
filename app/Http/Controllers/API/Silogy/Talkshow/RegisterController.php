<?php

namespace App\Http\Controllers\API\Silogy\Talkshow;

use App\Http\Controllers\Controller;
use App\Models\RegisterTalkshow;
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

            $check = RegisterTalkshow::where(['user_id' => Auth::user()->id, 'year' => $year])->count();
            $data = RegisterTalkshow::where(['user_id' => Auth::user()->id, 'year' => $year])->first();

            return response()->json([
                'is_registered' => $check > 0 ? true : false,
                'type' => $data->type
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
                'type' => 'required',
                'name' => 'required',
                'no_whatsapp' => 'required',
                'agency_name' => 'required',
                'province' => 'required',
                'regency' => 'required',
                'proof_himsika' => 'required',
                'proof_edufair' => 'required',
            ]);

            if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 400);

            if (RegisterTalkshow::where(['user_id' => Auth::user()->id, 'year' => $year])->count() > 0)
                return response()->json(['message' => 'you have registered'], 406);

            if ($request->type === 'offline') {
                if (RegisterTalkshow::where(['year' => $year, 'type' => 'offline'])->count() > 60)
                    return response()->json(['message' => 'registration is full'], 406);
            } else {
                if (RegisterTalkshow::where(['year' => $year, 'type' => 'online'])->count() > 300)
                    return response()->json(['message' => 'registration is full'], 406);
            }

            RegisterTalkshow::create([
                'type' => $request->type,
                'user_id' => Auth::user()->id,
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
            $participants_offline = RegisterTalkshow::where(['year' => $year, 'type' => 'offline'])->count();
            $participants_online = RegisterTalkshow::where(['year' => $year, 'type' => 'online'])->count();
            return response()->json([
                'total_participant' => [
                    'offline' => $participants_offline,
                    'online' => $participants_online
                ]
            ], 200);
        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request.'], 500);
        }

    }

}
