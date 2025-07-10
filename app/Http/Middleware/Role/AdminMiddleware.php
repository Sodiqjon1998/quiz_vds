<?php

namespace App\Http\Middleware\Role;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!empty(Auth::check())){
            if(Auth::user()->user_type == User::TYPE_ADMIN){
                return $next($request);
            }else{
                Auth::logout();
                return redirect('backend/login');
            }
        }else{
            Auth::logout();
            return redirect('backend/login');
        }
    }
}
