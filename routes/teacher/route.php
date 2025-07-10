<?php

use App\Http\Controllers\Teacher\AttachmentController;
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\QuizController;
use App\Http\Controllers\Teacher\ExamController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\SiteController;

Route::middleware(['auth.teacher','teacher'])->group(function () {

    Route::get('/teacher', [SiteController::class, 'index'])->name('teacher');

    Route::prefix('/teacher')->controller(QuizController::class)->group(function () {

        //QUIZ ROUTE
        Route::get('/teacher/quiz/index', 'index')->name('teacher.quiz.index');
        Route::get('/teacher/quiz/create', 'create')->name('teacher.quiz.create');
        Route::get('/teacher/quiz/{id}', 'show')->name('teacher.quiz.show');
        Route::post('/teacher/quiz/store', 'store')->name('teacher.quiz.store');
        Route::get('/teacher/quiz/{id}/edit', 'edit')->name('teacher.quiz.edit');
        Route::post('/teacher/quiz/{id}/update', 'update')->name('teacher.quiz.update');
        Route::delete('/teacher/quiz/{id}', 'destroy')->name('teacher.quiz.destroy');

    });

    Route::prefix('/teacher')->controller(AttachmentController::class)->group(function () {

        //ATTACHMENT ROUTE
        Route::get('/teacher/attachment/index', 'index')->name('teacher.attachment.index');
        Route::get('/teacher/attachment/create', 'create')->name('teacher.attachment.create');
        Route::get('/teacher/attachment/{id}', 'show')->name('teacher.attachment.show');
        Route::post('/teacher/attachment/store', 'store')->name('teacher.attachment.store');
        Route::get('/teacher/attachment/{id}/edit', 'edit')->name('teacher.attachment.edit');
        Route::post('/teacher/attachment/{id}/update', 'update')->name('teacher.attachment.update');
        Route::delete('/teacher/attachment/{id}', 'destroy')->name('teacher.attachment.destroy');

    });

    Route::prefix('/teacher')->controller(QuestionController::class)->group(function () {

        //ATTACHMENT ROUTE
        Route::get('/teacher/question/index', 'index')->name('teacher.question.index');
        Route::get('/teacher/question/create', 'create')->name('teacher.question.create');
        Route::get('/teacher/question/{id}', 'show')->name('teacher.question.show');
        Route::post('/teacher/question/store', 'store')->name('teacher.question.store');
        Route::get('/teacher/question/{id}/edit', 'edit')->name('teacher.question.edit');
        Route::post('/teacher/question/{id}/update', 'update')->name('teacher.question.update');
        Route::delete('/teacher/question/{id}', 'destroy')->name('teacher.question.destroy');

    });

    Route::prefix('/teacher')->controller(ExamController::class)->group(function () {

        //EXAM ROUTE
        Route::get('/teacher/exam/index', 'index')->name('teacher.exam.index');
        Route::get('/teacher/exam/create', 'create')->name('teacher.exam.create');
        Route::get('/teacher/exam/{quiz_id}/{subject_id}', 'show')->name('teacher.exam.show');
        Route::get('/teacher/exam/{id}', 'showTest')->name('teacher.exam.showTest');
        Route::post('/teacher/exam/store', 'store')->name('teacher.exam.store');
        Route::get('/teacher/exam/{id}/edit', 'edit')->name('teacher.exam.edit');
        Route::post('/teacher/exam/{id}/update', 'update')->name('teacher.exam.update');
        Route::delete('/teacher/exam/{id}', 'destroy')->name('teacher.exam.destroy');

    });
});
