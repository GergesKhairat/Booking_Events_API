<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\OtpService;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    protected $otpService;
    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    public function verifyOtp(Request $request)
    {
        $valid = Otp::where('email', $request->email)->where('otp', $request->otp)->first();
        if (!$valid) {
            return response()->json([
                "success" => false,
                "mssage" => "Invalid OTP"
            ], 400);
        }
        if (Carbon::now()->greaterThan($valid->expires_at)) {
            return response()->json([
                "success" => false,
                "mssage" => "OTP Expired"
            ], 400);
        }
        User::where('email', $request->email)->update(['email_verified_at' => Carbon::now()]);
        return response()->json([
            "success" => true,
            "mssage" => "Email Verified"
        ], 200);
    }
    public function resend(Request $request)
    {
        $this->otpService->sendOtp($request);
        return response()->json([
            'success' => true,
            "message" => "Otp resent successfully"
        ]);
    }
}
