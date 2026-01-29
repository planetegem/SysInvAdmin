<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Invitation;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;





class RegisterController extends Controller
{
    // Invite a new user to use this marvel of a CMS
    public static function invite(string $mail, string $secret)
    {
        $invitation = new Invitation();
        try {
            $invitation->id = Str::uuid();
            $invitation->email = $mail;
            $invitation->token = hash('tiger128,4', $secret);
            $invitation->redeemed = false;
            $invitation->save();
        } catch(Exception $e){
            return "Couldn't save invitation: {$e->getMessage()}.";
        }

        try {
            $message = "test";
            send_system_mail($mail, $message, "Welcome");
            $response = "Instructions sent to {$mail}: ";
        } catch (Exception $e) {
            $response = "Couldn't send mail: {$e->getMessage()}. Send these instructions: ";
        }
        $response = $response . "visit /register/{$invitation->id} to set your password. Your secret token is {$invitation->token}.";
        return $response;
    }


    // Make admin user (only done via artisan command)
    public static function makeAdmin(string $mail, string $password)
    {
        $user = new User();
        $user->email = $mail;
        $user->password = Hash::make($password);
        $user->role = 'admin';
        $user->save();
    }

    // Accept invitation: display form to set password
    public function create(string $uuid)
    {
        $invitation = Invitation::where('id', $uuid)->first();
        if ($invitation == null) {
            abort(403);
        }

        return view('auth.register', compact('invitation'));
    }

    // Create new user
    public function store(Request $request, string $uuid)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::exists('invitations', 'email')
            ],
            'password' => ['required'],
            'confirm-password' => ['required', 'same:password']
        ]);

        $invitation = Invitation::where('id', $uuid)->first();
        if ($invitation == null) {
            return abort(403);
        }

        if ($invitation->email != $validated['email']) {
            return Redirect::back()->withErrors(['email' => 'Your email address is incorrect.']);
        }

        // If account was already created; NOT TESTED
        if ($invitation->redeemed) {
            return redirect(route('login'))->withErrors(['general' => 'Welcome! You appear to have already created your account.']);
        } else {
            $invitation->redeemed = true;
            $invitation->save();

            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'user'
            ]);
            event(new Registered($user));
            Auth::login($user);

            return redirect(route('home', absolute: false));
        }
    }

}
