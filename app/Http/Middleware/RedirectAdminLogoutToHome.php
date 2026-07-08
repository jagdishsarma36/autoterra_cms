<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminLogoutToHome
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->is('*/logout') && ! Auth::check()) {
            return redirect('/');
        }

        return $response;
    }
}
