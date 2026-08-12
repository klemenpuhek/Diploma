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
        return view('adminLogin');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($credentials['username'] !== Admin::username || $credentials['password'] !== Admin::password) {
            return back()->withErrors(['username' => 'Napačno uporabniško ime ali geslo.']);
        }

        $request->session()->regenerate();
        $request->session()->put('adminLoggedIn', true);

        return redirect()->route('adminPanel');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('adminLoggedIn');
        $request->session()->regenerate();

        return redirect()->route('login');
    }
}
