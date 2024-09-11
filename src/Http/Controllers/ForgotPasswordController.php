<?php

namespace Wink\Http\Controllers;

use Throwable;
use Wink\WinkAuthor;
use Illuminate\Support\Str;
use Wink\Mail\ResetPasswordEmail;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showResetRequestForm()
    {
        return view('wink::request-password-reset');
    }

    public function sendResetLinkEmail()
    {
        validator(request()->all(), [
            'email' => 'required|email',
        ])->validate();

        if ($author = WinkAuthor::whereEmail(request('email'))->first()) {
            cache(['password.reset.'.$author->id => $token = Str::random()],
                now()->addMinutes(30)
            );

            Mail::to($author->email)->send(new ResetPasswordEmail(
                encrypt($author->id.'|'.$token)
            ));
        }

        return redirect()->route('wink.password.forgot')->with('sent', true);
    }

    public function showNewPassword($token)
    {
        try {
            $token = decrypt($token);

            [$authorId, $token] = explode('|', $token);

            $author = WinkAuthor::findOrFail($authorId);
        } catch (Throwable $e) {
            return redirect()->route('wink.password.forgot')->with('invalidResetToken', true);
        }

        if (cache('password.reset.'.$authorId) != $token) {
            return redirect()->route('wink.password.forgot')->with('invalidResetToken', true);
        }

        cache()->forget('password.reset.'.$authorId);

        $author->password = Hash::make($password = Str::random());

        $author->save();

        return view('wink::reset-password', [
            'password' => $password,
        ]);
    }
}
