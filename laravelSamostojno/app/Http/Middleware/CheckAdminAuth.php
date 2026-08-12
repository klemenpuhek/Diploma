<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('adminLoggedIn')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
