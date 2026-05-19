<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\OtpService;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $otpService;
    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    //register
    public function register(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(), [
            'name' => "required|string|max:255",
            'email' => "required|email|max:255|unique:users,email",
            'password' => "required|min:8",
        ]);
        //if error ->response
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "errors" => $validator->errors()
            ], 422);
        }

        //hash the password
        $hashPassword = Hash::make($request->password);

        //creating the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $hashPassword,
        ]);


        //creating authentication token
        $token = $user->createToken('ApiToken')->plainTextToken;
        //sending OTP
        $this->otpService->sendOtp($request);
        //response
        return response()->json([
            "success" => true,
            "message" => "user registered successfully",
            "token" => $token
        ], 201);
    }
    public function login(LoginRequest $request)
    {
        $request->validated();
        //check on user
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json([
                "success" => false,
                "message" => "invalid credentials"
            ], 403);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user->email_verified_at) {
            return response()->json([
                "success" => false,
                "message" => "Email is not verified"
            ], 403);
        }
        $token = $user->createToken('ApiToken')->plainTextToken;
        return response()->json([
            "success" => true,
            "token" => $token,
            "user" => new UserResource($user)
        ], 200);
    }
    //logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "success" => true,
            "message" => "logged out successfully"
        ], 200);
    }
}
