<?php

namespace App\Http\Middleware\Role;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!empty(Auth::check())) {
            if (Auth::user()->user_type == User::TYPE_TEACHER) {
                return $next($request);
            } else {
                Auth::logout();
                return redirect('teacher/login');
            }
        } else {
            Auth::logout();
            return redirect('teacher/login');
        }
    }
}
