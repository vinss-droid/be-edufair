<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        DB::beginTransaction();

        $admins = [
            'kevinsipahutar220604@gmail.com',
            'ikycollege@gmail.com',
            'moniqcasandha04@gmail.com',
            'arielss9898@gmail.com'
        ];

        try {

            $validation = Validator::make($request->all(), [
                'gid' => 'required',
                'name' => 'required',
                'email' => 'required',
                'user_picture' => 'required'
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()], 400);
            }

            $user = User::updateOrCreate([
                // 'gid' => $request->gid,
                'email' => $request->email
            ], [
                'gid' => $request->gid,
                'name' => ucwords(strtolower($request->name)),
                'email' => $request->email,
                'picture' => $request->user_picture,
                'password' => Hash::make(today() . $request->gid),
                'email_verified_at' => today(),
                'is_admin' => in_array($request->email, $admins) ? true : false,
            ]);

            DB::commit();


            return response()->json([
                'token' => $user->createToken('token')->plainTextToken,
                'expired' => null,
                'user' => new UserResource(User::findOrFail($user->id))
            ], 200);

        } catch (\Exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'failed to processing request.'
            ], 500);
        }

    }

    public function logout()
    {

        try {

            Auth::user()->currentAccessToken()->delete();

            return response()->json(['message' => 'success logout'], 200);

        } catch (\Exception) {
            return response()->json([
                'message' => 'failed to processing request.'
            ], 500);
        }

    }

}
