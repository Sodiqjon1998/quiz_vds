<?php

use App\Http\Controllers\Frontend\SiteController;

Route::get('/', function(){
    return view('frontend.site.crm');
});

Route::get('/register', [SiteController::class, 'register']);
Route::post('/register', [SiteController::class, 'store'])->name('frontend.register.store');
Route::get('/success/{id}', [SiteController::class, 'success'])->name('frontend.site.success');
