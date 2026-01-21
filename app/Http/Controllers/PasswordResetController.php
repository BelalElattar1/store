<?php

namespace App\Http\Controllers; 

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PasswordResetCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PasswordResetController {

    public function send_reset_code(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:50|exists:users,email',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        PasswordResetCode::whereEmail($request['email'])->delete();

        $code = PasswordResetCode::create([
            'email'      => $request['email'],
            'code'       => rand(100000, 999999),
            'expires_at' => Carbon::now()->addMinutes(10)
        ]);

        Mail::raw("Your Password Reset Code Is: $code->code", function ($message) use ($request) {
            $message->to($request['email'])
                    ->subject('Code Sent Your Email');
        });

        return response()->json([
            'Message' => 'The code has been sent to your email successfully'
        ]);

    }

    public function reset_password(Request $request) {

        $validator = Validator::make($request->all(), [
            'email'     => 'required|string|email|max:50|exists:password_reset_codes,email',
            'code'      => 'required|string|max:6|min:6|exists:password_reset_codes,code',
            'password'  => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $reset = PasswordResetCode::where('email', $request['email'])
                ->where('code', $request['code'])
                ->where('expires_at', '>=', Carbon::now())
                ->firstOrFail();

        if(!$reset) {
            return response()->json([
                'Message' => 'Code Is Expired'
            ]);
        }

        User::whereEmail($reset->email)->update([
            'password' => Hash::make($request['password']),
        ]);

        $reset->delete();

        return response()->json([
            'Message' => 'Password Has Been Reset Suc'
        ]);
    }

}