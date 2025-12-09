<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'backend/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://quizvds.up.railway.app',
        'https://andijonyuksalish.netlify.app',
        'http://localhost:5173',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Bu muhim!
];
