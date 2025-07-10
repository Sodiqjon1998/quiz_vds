<?php

use App\Http\Controllers\Student\QuizController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\SiteController;

Route::middleware(['auth.student', 'student'])->group(function () {

    Route::get('/student', [SiteController::class, 'index'])->name('student');

    Route::prefix('/student')->controller(QuizController::class)->group(function () {

        //QUIZ ROUTE
        Route::get('/quiz/index', 'index')->name('student.quiz.index');
        Route::get('/quiz/create', 'create')->name('student.quiz.create');
        Route::get('/quiz/{id}/{subjectId}/show', 'show')->name('student.quiz.show');
        Route::post('/quiz/store', 'store')->name('student.quiz.store');
        Route::get('/quiz/{id}/edit', 'edit')->name('student.quiz.edit');
        Route::post('/quiz/{id}/update', 'update')->name('student.quiz.update');
        Route::delete('/quiz/{id}', 'destroy')->name('student.quiz.destroy');

        Route::post('/student/quiz/saveTime', [QuizController::class, 'saveTime'])->name('student.quiz.saveTime');
        Route::get('/student/quiz/get-time', [QuizController::class, 'getTime'])->name('student.quiz.getTime');
        Route::post('/student/quiz/clear-time', [QuizController::class, 'clearTime'])->name('student.quiz.clearTime');

        Route::get('/quiz/{id}/result', [QuizController::class, 'result'])->name('student.quiz.result');



        Route::post('/student/quiz/save-state', [QuizController::class, 'saveAttemptState'])->name('student.quiz.saveState');
        Route::get('/student/quiz/{quizId}/get-state', [QuizController::class, 'getAttemptState'])->name('student.quiz.getState');
    });
});
