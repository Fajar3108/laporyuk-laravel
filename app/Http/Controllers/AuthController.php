<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nik' => ['required', 'size:16'],
            'name' => ['required', 'min:3'],
            'username' => ['required', 'min:6', 'max:12', Rule::unique('users')],
            'email' => ['required', 'email', Rule::unique('users')],
            'password' => ['required', 'min:8', 'max:16', 'confirmed'],
        ]);

        if ($validate->fails()) return ResponseBuilder::buildErrorResponse('Your request is invalid',  $validate->errors(), 400);

        $user = User::create([
            'role_id' => 3,
            'nik' => $request->nik,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return ResponseBuilder::buildResponse('User registered successfully', [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'max:16'],
        ]);

        if ($validate->fails()) return ResponseBuilder::buildErrorResponse('Your request is invalid',  $validate->errors(), 400);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password]))  return ResponseBuilder::buildErrorResponse('Invalid email or password',  [], 400);;

        $user = User::where('email', $request->email)->first();

        return ResponseBuilder::buildResponse('Login Success', [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }
}
