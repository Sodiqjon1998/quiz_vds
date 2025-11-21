<?php

use App\Http\Controllers\Koordinator\ExamController;
use App\Http\Controllers\Koordinator\SiteController;
use App\Http\Livewire\Koordinator\ExamResults;

Route::middleware(['auth.koordinator', 'koordinator'])->group(function () {

    Route::get('/koordinator', [SiteController::class, 'index'])->name('koordinator');

    Route::get('/exam-results', [ExamController::class, 'index'])->name('koordinator.exam-results');

    Route::get('/koordinator/exam/monitoring', function () {
        return view('koordinator.exam.monitoring');
    })->name('koordinator.exam.monitoring');
});
