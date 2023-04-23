<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UploadController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth.apikey', 'auth:sanctum']);
    }

    public function userPath()
    {
        return storage_path() . '/app/public/proof_file/' . Auth::user()->id;
    }

    public function uploadProofEdufair(Request $request)
    {

        try {

            $validation = Validator::make($request->all(), [
                'image' => 'required|max:5120|mimes:jpg,png,jpeg,bmp,webp'
            ]);

            if ($validation->fails())
                return response()->json(['errors' => $validation->errors()], 400);

            $file = $request->image;
            $newFileName = Str::uuid() . '_' . $file->getClientOriginalName();

            if (!File::exists($this->userPath()))
                File::makeDirectory($this->userPath(), 0775, true);

            $file->move($this->userPath(), $newFileName);

            return $newFileName;

        } catch (\Exception) {
            return response()->json(['message' => 'failed to processing request!'], 500);
        }

    }

}
