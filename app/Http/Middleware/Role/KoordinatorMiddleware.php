<?php

namespace App\Http\Middleware\Role;

use App\Models\Users;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class KoordinatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Autentifikatsiya tekshiruvi
        if (!Auth::check()) {
            return redirect('koordinator/login')->with('error', 'Iltimos, tizimga kiring');
        }

        $user = Auth::user();

        // 2. Rol tekshiruvi
        if ($user->user_type != Users::TYPE_KOORDINATOR) {
            return redirect('/')->with('error', 'Sizda bu sahifaga kirish huquqi yo\'q');
        }

        // 3. Status tekshiruvi
        if ($user->status != Users::STATUS_ACTIVE) {
            Auth::logout();
            return redirect('koordinator/login')->with('error', 'Sizning akkauntingiz bloklangan');
        }

        return $next($request);
    }
}