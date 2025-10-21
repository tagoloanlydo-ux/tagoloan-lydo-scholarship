<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\Lydopers;

class ForgotPasswordController extends Controller
{
    // Show forgot password form
    public function showForgotPasswordForm() {
        return view('auth.forgot-password');
    }

    // Send reset link email
    public function sendResetLinkEmail(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:tbl_lydopers,lydopers_email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['lydopers_email' => __($status)]);
    }

    // Show reset password form
    public function showResetForm($token) {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Handle password reset
    public function reset(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:lydopers,lydopers_email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email','password','password_confirmation','token'),
            function ($user, $password) {
                $user->lydopers_pass = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password has been reset successfully!')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
