<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;





class LoginController extends Controller
{
    // SHOW LOGIN SCREEN
    public function login(): View
    {
        return view('auth.login');
    }

    // AUTHENTICATE USER
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required']
        ]);
        
        if (Auth::attempt($credentials, $request->filled('remember'))){
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        return Redirect::back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ]);
    }

    // LOG OUT
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors(['logout'=>'You have been logged out.']);
    }
}
