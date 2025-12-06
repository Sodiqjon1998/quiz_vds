<?php

return [
    // ... boshqa sozlamalar

    'temporary_file_upload' => [
        'disk' => env('LIVEWIRE_TMP_DISK', 'local'),
        'rules' => ['file', 'mimes:xlsx,xls,csv,pdf', 'max:10240'], // 10MB
        'directory' => 'livewire-tmp',
        'middleware' => null,
        'preview_mimes' => [],
        'max_upload_time' => 10, // 10 daqiqa
    ],
];
