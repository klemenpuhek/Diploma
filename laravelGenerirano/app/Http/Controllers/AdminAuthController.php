<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($credentials['username'] !== Admin::USERNAME || $credentials['password'] !== Admin::PASSWORD) {
            return back()
                ->withErrors(['username' => 'Napačno uporabniško ime ali geslo.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put('admin_logged_in', true);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        
        $request->session()->forget('admin_logged_in');
        $request->session()->regenerate();

        return redirect()->route('admin.login');
    }
}
