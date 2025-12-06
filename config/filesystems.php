<?php

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // ✅ RAILWAY UCHUN: Livewire temp fayllar
        'livewire-tmp' => [
            'driver' => 'local',
            'root' => storage_path('app/livewire-tmp'),
            'visibility' => 'private',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        'b2' => [
            'driver' => 's3',
            'key' => env('BACKBLAZE_KEY_ID'),
            'secret' => env('BACKBLAZE_APPLICATION_KEY'),
            'region' => env('BACKBLAZE_REGION', 'us-east-005'),
            'bucket' => env('BACKBLAZE_BUCKET_NAME'),
            'endpoint' => env('BACKBLAZE_ENDPOINT', 'https://s3.us-east-005.backblazeb2.com'),
            'use_path_style_endpoint' => false,
            'throw' => true,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
