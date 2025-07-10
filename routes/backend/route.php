<?php

use App\Http\Controllers\Backend\SiteController;
use App\Http\Controllers\Backend\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ClassesController;

Route::middleware(['auth', 'verified', 'admin'])->group(function () {

    Route::get('/dashboard', [SiteController::class, 'index'])->name('dashboard');


    Route::prefix('/dashboard')->controller(UsersController::class)->group(function () {

        //USERS ROUTE
        Route::get('/backend/user/index', 'index')->name('backend.user.index');
        Route::get('/backend/user/create', 'create')->name('backend.user.create');
        Route::get('/backend/user/{id}', 'show')->name('backend.user.show');
        Route::post('/backend/user/store', 'store')->name('backend.user.store');
        Route::get('/backend/user/{id}/edit', 'edit')->name('backend.user.edit');
        Route::post('/backend/user/{id}/update', 'update')->name('backend.user.update');
        Route::delete('/backend/user/{id}', 'destroy')->name('backend.user.destroy');

    });

    Route::prefix('/dashboard')->controller(\App\Http\Controllers\Backend\StudentController::class)->group(function () {

        //USERS ROUTE
        Route::get('/backend/student/index', 'index')->name('backend.student.index');
        Route::get('/backend/student/create', 'create')->name('backend.student.create');
        Route::get('/backend/student/{id}', 'show')->name('backend.student.show');
        Route::post('/backend/student/store', 'store')->name('backend.student.store');
        Route::get('/backend/student/{id}/edit', 'edit')->name('backend.student.edit');
        Route::post('/backend/student/{id}/update', 'update')->name('backend.student.update');
        Route::delete('/backend/student/{id}', 'destroy')->name('backend.student.destroy');

    });


    Route::prefix('/dashboard')->controller(ClassesController::class)->group(function () {

        //CLASSES ROUTE
        Route::get('/backend/classes/index', 'index')->name('backend.classes.index');
        Route::get('/backend/classes/create', 'create')->name('backend.classes.create');
        Route::get('/backend/classes/{id}', 'show')->name('backend.classes.show');
        Route::post('/backend/classes/store', 'store')->name('backend.classes.store');
        Route::get('/backend/classes/{id}/edit', 'edit')->name('backend.classes.edit');
        Route::post('/backend/classes/{id}/update', 'update')->name('backend.classes.update');
        Route::delete('/backend/classes/{id}', 'destroy')->name('backend.classes.destroy');

    });


    Route::prefix('/dashboard')->controller(\App\Http\Controllers\Backend\SubjectsController::class)->group(function () {

        //SUBJECTS ROUTE
        Route::get('/backend/subjects/index', 'index')->name('backend.subjects.index');
        Route::get('/backend/subjects/create', 'create')->name('backend.subjects.create');
        Route::get('/backend/subjects/{id}', 'show')->name('backend.subjects.show');
        Route::post('/backend/subjects/store', 'store')->name('backend.subjects.store');
        Route::get('/backend/subjects/{id}/edit', 'edit')->name('backend.subjects.edit');
        Route::post('/backend/subjects/{id}/update', 'update')->name('backend.subjects.update');
        Route::delete('/backend/subjects/{id}', 'destroy')->name('backend.subjects.destroy');

    });
});
