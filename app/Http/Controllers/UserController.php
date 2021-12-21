<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nik' => ['required', 'size:16'],
            'name' => ['required', 'min:3'],
            'username' => ['required', 'min:6', 'max:12', Rule::unique('users')],
            'email' => ['required', 'email', Rule::unique('users')],
            'password' => ['required', 'min:8', 'max:16', 'confirmed'],
        ]);

        if ($validate->fails()) return ResponseBuilder::buildErrorResponse('Your request is invalid',  $validate->errors(), 400);

        User::create([
            'role_id' => 2,
            'nik' => $request->nik,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return ResponseBuilder::buildResponse('User created successfully', []);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) return ResponseBuilder::buildErrorResponse('User not found', [], 404);

        $user->delete();

        return ResponseBuilder::buildResponse('User Deleted successfuly', []);
    }
}
