<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Show Register/Create form
    public function create() {
        return view('users.register');
    }

    // create new user
    public function store(Request $req) {
        $formFields = $req->validate([
            "name" => ['required', 'min:3'],
            "email" => ['required', 'email', Rule::unique('users', 'email')],
            "password" => 'required|confirmed|min:6'
        ]);

        // Hash password
        $formFields['password'] = bcrypt($formFields['password']);

        $user = User::create($formFields);
        
        // Login
        auth()->login($user);
        return redirect('/')->with('message', 'User created and logged in!');
    }

    // Logout user
    public function logout(Request $req) {
        auth()->logout();

        $req->session()->invalidate();
        $req->session()->regenerateToken();

        return redirect('/')->with('message', 'You have been logged out!');
    }

    // Show login form
    public function login() {
        return view('users.login');
    }

    // Login a user
    public function authenticate(Request $req) {
        $formFields = $req->validate([
            "email" => ['required', 'email'],
            "password" => 'required'
        ]);

        if (auth()->attempt($formFields)) {
            $req->session()->regenerate();

            return redirect('/')->with('message', 'You are logged in!');
        }

        return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }
}
