<?php

namespace App\Http\Controllers\API\Workshop;

use App\Http\Controllers\Controller;
use App\Models\RegisterWorkshop;
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

            $check = RegisterWorkshop::where(['user_id' => Auth::user()->id, 'year' => $year])->count();

            return response()->json([
                'is_registered' => $check > 0 ? true : false
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
                'no_whatsapp' => 'required',
                'agency_name' => 'required',
                'province' => 'required',
                'regency' => 'required',
                'proof_himsika' => 'required',
                'proof_edufair' => 'required',
            ]);

            if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 400);

            if (RegisterWorkshop::where(['user_id' => Auth::user()->id, 'year' => $year])->count() > 0)
                return response()->json(['message' => 'you already registered'], 406);

            RegisterWorkshop::create([
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
        return asset('/storage/proof_file' . '/' . Auth::user()->id);
    }
}
