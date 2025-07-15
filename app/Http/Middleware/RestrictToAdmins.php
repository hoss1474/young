<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictToAdmins
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }
        return redirect('/home'); // ریدایرکت کاربران غیرمجاز به صفحه اصلی سایت
    }
}
