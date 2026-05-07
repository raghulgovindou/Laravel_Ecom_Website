<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
 public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {

        // important: prevent session fixation
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    return back()->with('error', 'Invalid email or password');
}
public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/signin');
}
public function register(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
           'password' => $request->password,
            'role' => 'worker',
        ]);

        return redirect('/signin')->with('success', 'Registration successful');
    }

}
