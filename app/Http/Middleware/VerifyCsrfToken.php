<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*',  // ✅ API route'lari uchun CSRF o'chirilgan
        'broadcasting/auth',  // ✅ MUHIM! Pusher auth uchun
        'backend/login',
        'teacher/login',
    ];
}
