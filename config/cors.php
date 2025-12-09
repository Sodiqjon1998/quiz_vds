<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // <-- Eng muhimi shu! Hamma joydan ruxsat berish.
    'allowed_origins_patterns' => [],
    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'X-CSRF-TOKEN',
        'X-Socket-Id',  // ✅ Pusher uchun kerak
        'Accept',
    ],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,

];
