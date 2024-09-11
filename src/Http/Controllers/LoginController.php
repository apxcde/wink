<?php

namespace Wink\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController
{
    public function showLoginForm()
    {
        return view('wink::login');
    }

    public function login()
    {
        validator(request()->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($this->guard()->attempt(
            request()->only('email', 'password'),
            request()->filled('remember')
        )) {
            return redirect('/'.config('wink.path'));
        }

        throw ValidationException::withMessages([
            'email' => ['Invalid email or password!'],
        ]);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect()->route('wink.auth.login')->with('loggedOut', true);
    }

    protected function guard()
    {
        return Auth::guard('wink');
    }
}
