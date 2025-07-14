<?php

use App\Http\Controllers\Frontend\SiteController;

Route::get('/', function(){
    return view('frontend.site.crm');
});

Route::get('/regiter', [SiteController::class, 'register']);
Route::post('/regiter', [SiteController::class, 'store'])->name('frontend.register.store');
Route::get('/success/{id}', [SiteController::class, 'success'])->name('frontend.site.success');
