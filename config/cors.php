<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://quizvds.up.railway.app',
        'http://localhost:3000', // development uchun
    ], // <-- Eng muhimi shu! Hamma joydan ruxsat berish.
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,

];
