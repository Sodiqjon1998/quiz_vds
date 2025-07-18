<?php

use App\Http\Controllers\Teacher\AttachmentController;
use App\Http\Controllers\Teacher\ClassesController;
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\QuizController;
use App\Http\Controllers\Teacher\ExamController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\SiteController;
use App\Http\Controllers\Teacher\UserController;

Route::middleware(['auth.teacher', 'teacher'])->group(function () {

    Route::get('/teacher', [SiteController::class, 'index'])->name('teacher');

    Route::prefix('/teacher')->controller(QuizController::class)->group(function () {

        //QUIZ ROUTE
        Route::get('/quiz/index', 'index')->name('teacher.quiz.index');
        Route::get('/quiz/create', 'create')->name('teacher.quiz.create');
        Route::get('/quiz/{id}', 'show')->name('teacher.quiz.show');
        Route::post('/quiz/store', 'store')->name('teacher.quiz.store');
        Route::get('/quiz/{id}/edit', 'edit')->name('teacher.quiz.edit');
        Route::post('/quiz/{id}/update', 'update')->name('teacher.quiz.update');
        Route::delete('/quiz/{id}', 'destroy')->name('teacher.quiz.destroy');
    });

    Route::prefix('/teacher')->controller(AttachmentController::class)->group(function () {

        //ATTACHMENT ROUTE
        Route::get('/attachment/index', 'index')->name('teacher.attachment.index');
        Route::get('/attachment/create', 'create')->name('teacher.attachment.create');
        Route::get('/attachment/{id}', 'show')->name('teacher.attachment.show');
        Route::post('/attachment/store', 'store')->name('teacher.attachment.store');
        Route::get('/attachment/{id}/edit', 'edit')->name('teacher.attachment.edit');
        Route::post('/attachment/{id}/update', 'update')->name('teacher.attachment.update');
        Route::delete('/attachment/{id}', 'destroy')->name('teacher.attachment.destroy');
    });

    Route::prefix('/teacher')->controller(QuestionController::class)->group(function () {

        //QUESTION ROUTE
        Route::get('/question/index', 'index')->name('teacher.question.index');
        Route::get('/question/{quiz_id}/create', 'create')->name('teacher.question.create');
        Route::get('/question/{id}', 'show')->name('teacher.question.show');
        Route::post('/question/store', 'store')->name('teacher.question.store');
        Route::get('/question/{id}/edit', 'edit')->name('teacher.question.edit');
        Route::put('/question/{id}', 'update')->name('teacher.question.update');
        Route::delete('/question/{id}', 'destroy')->name('teacher.question.destroy');

        Route::get('/questions/import-file', 'importFile')->name('teacher.question.importFile');
        Route::post('/questions/import', 'import')->name('teacher.question.import');
    });

    Route::prefix('/teacher')->controller(ExamController::class)->group(function () {

        //EXAM ROUTE
        Route::get('/exam/index', 'index')->name('teacher.exam.index');
        Route::get('/exam/get-result', 'getResult')->name('teacher.exam.getResult');
        Route::get('/exam/show/{quiz_id}/{subject_id}', 'show')->name('teacher.exam.show');
        Route::get('/exam/{id}', 'showTest')->name('teacher.exam.showTest');
        Route::post('/exam/store', 'store')->name('teacher.exam.store');
        Route::get('/exam/{id}/edit', 'edit')->name('teacher.exam.edit');
        Route::post('/exam/{id}/update', 'update')->name('teacher.exam.update');
        Route::delete('/exam/{id}', 'destroy')->name('teacher.exam.destroy');
    });


    Route::prefix('/teacher')->controller(UserController::class)->group(function () {

        //EXAM ROUTE
        Route::get('user/index', 'index')->name('teacher.user.index');
        Route::get('user/setting', 'setting')->name('teacher.user.setting');
        // Route::get('user/{quiz_id}/{subject_id}', 'show')->name('teacher.user.show');
        // Route::get('user/{id}', 'showTest')->name('teacher.user.showTest');
        // Route::post('user/store', 'store')->name('teacher.user.store');
        // Route::get('user/{id}/edit', 'edit')->name('teacher.user.edit');
        Route::post('user/update', 'update')->name('teacher.user.update');
        // Route::delete('user/{id}', 'destroy')->name('teacher.user.destroy');

    });


    Route::prefix('/teacher')->controller(ClassesController::class)->group(function () {

        //CLASSES ROUTE
        Route::get('/classes/index', 'index')->name('teacher.classes.index');
        Route::get('/classes/get-result', 'getResult')->name('teacher.classes.getResult');
        Route::get('/classes/show/{id}', 'show')->name('teacher.classes.show');
        Route::get('/classes/{id}', 'showTest')->name('teacher.classes.showTest');
        Route::post('/classes/store', 'store')->name('teacher.classes.store');
        Route::get('/classes/{id}/edit', 'edit')->name('teacher.classes.edit');
        Route::post('/classes/{id}/update', 'update')->name('teacher.classes.update');
        Route::delete('/classes/{id}', 'destroy')->name('teacher.classes.destroy');
    });
});
