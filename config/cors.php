<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // MANA SHU YERGA E'TIBOR BERING:
    'allowed_origins' => ['*'], // '*' qilsangiz hamma joydan ruxsat beradi (Test uchun eng osoni)

    // Yoki xavfsizroq varianti (o'zingizni domenni yozasiz):
    // 'allowed_origins' => ['http://localhost:5173', 'https://sizning-react-saytingiz.railway.app'],

    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Agar cookie/session ishlatsangiz true bo'lishi kerak
];
