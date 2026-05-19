<?php

namespace App\Http\Services;

use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function sendOtp(Request $request)
    {
        $otp = rand(100000, 999999);
        Otp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10)
        ]);
        Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)->subject('Email Verification Code');
        });
    }
    
}
