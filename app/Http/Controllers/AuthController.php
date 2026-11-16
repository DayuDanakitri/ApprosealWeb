<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Display the login page.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle user authentication.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Pastikan akun memiliki role.
        if (!$user->role) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun belum memiliki role. Hubungi administrator.',
                ]);
        }

        return $this->redirectByRole($user->role->slug);
    }

    /**
     * Redirect user according to their role.
     */
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'secretary' => redirect()->route('secretary.dashboard'),
            'approver' => redirect()->route('approver.dashboard'),
            'administrator' => redirect()->route('administrator.dashboard'),
            'verifier' => redirect()->route('verifier.dashboard'),
            default => abort(403, 'Role pengguna tidak dikenali.'),
        };
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Anda berhasil logout.');
    }
}
