<?php

use App\Http\Controllers\Koordinator\ExamController;
use App\Http\Controllers\Koordinator\SiteController;
use App\Http\Livewire\Koordinator\ExamResults;
use App\Http\Livewire\Koordinator\Report\NonReadingRecords;

Route::middleware(['auth.koordinator', 'koordinator'])->group(function () {

    // Bosh sahifa
    Route::get('/koordinator', [SiteController::class, 'index'])->name('koordinator');

    // Imtihon natijalari
    Route::get('/koordinator/exam-results', [ExamController::class, 'index'])->name('koordinator.exam-results');

    // Imtihon monitoringi
    Route::get('/koordinator/exam/monitoring', function () {
        return view('koordinator.exam.monitoring');
    })->name('koordinator.exam.monitoring');


    // Kunlik vazifalar hisobotlari
    Route::get('/koordinator/report/performance', function () {
        return view('koordinator.report.performance');
    })->name('koordinator.report.performance');


    // Kitobxonlik uchun
    Route::get('/koordinator/reading-records', function () {
        return view('koordinator.report.reading-records');
    })->name('koordinator.reading.records');


    // ✅ YANGI: Kitob tashlamaganlar hisoboti
    Route::get('/koordinator/non-readers-report', function () {
        return view('koordinator.report.non-readers-report');
    })->name('koordinator.non-readers-report');

});
