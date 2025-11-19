<?php

use App\Http\Controllers\Koordinator\SiteController;

Route::middleware(['auth.koordinator', 'koordinator'])->group(function () {

    Route::get('/koordinator', [SiteController::class, 'index'])->name('koordinator');

});