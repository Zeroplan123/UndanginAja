<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Cek apakah sudah ada admin di database
        $hasAdmin = User::where('role', 'admin')->exists();
        
        return view('auth.register', compact('hasAdmin'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Cek apakah sudah ada admin
        $hasAdmin = User::where('role', 'admin')->exists();
        
        // Tentukan role yang diizinkan
        $allowedRoles = ['user', 'vendor'];
        if (!$hasAdmin) {
            $allowedRoles[] = 'admin';
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
        ]);

        // Jika role admin dipilih tapi sudah ada admin, paksa jadi user
        $role = $request->role;
        if ($role === 'admin' && $hasAdmin) {
            $role = 'user';
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}