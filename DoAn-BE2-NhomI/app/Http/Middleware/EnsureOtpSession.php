<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('register')->with('error', 'Vui lòng đăng ký trước.');
        }

        return $next($request);
    }
}
