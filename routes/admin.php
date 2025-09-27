<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', 'admin')->group(function () {
    Route::prefix('admin')->as('admin.')->group(function () {
        Route::get('/home', function () {
            return view('pages.admin.home');
        })->name('home');

        Route::controller(StudentController::class)->group(function () {
            Route::prefix('students')->as('students.')->group(function () {
                Route::get('/students', 'index')->name('index');
                Route::get('/students/create', 'create')->name('create');
                Route::post('/students', 'store')->name('store');
                Route::get('/students/{student}', 'show')->name('show');
                Route::get('/students/{student}/edit', 'edit')->name('edit');
                Route::put('/students/{student}', 'update')->name('update');
                Route::delete('/students/{student}', 'destroy')->name('destroy');
            });
        });
        Route::controller(QuestionController::class)->group(function () {
            Route::prefix('questions')->as('questions.')->group(function () {
                Route::get('/index', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/{question}/edit', 'edit')->name('edit');
                Route::put('/{question}', 'update')->name('update');
                Route::delete('/{question}', 'destroy')->name('destroy');
                Route::get('/{question}/show', 'show')->name('show');
            });
        });

        Route::controller(FacultyController::class)->group(function () {
            Route::prefix('faculties')->as('faculties.')->group(function () {
                Route::get('/index', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/{faculty}/edit', 'edit')->name('edit');
                Route::put('/{faculty}', 'update')->name('update');
                Route::delete('/{faculty}', 'destroy')->name('destroy');
            });
        });

        Route::controller(GroupController::class)->group(function () {
            Route::prefix('groups')->as('groups.')->group(function () {
                Route::get('/index', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/{group}/edit', 'edit')->name('edit');
                Route::put('/{group}', 'update')->name('update');
                Route::delete('/{group}', 'destroy')->name('destroy');
            });
        });

        Route::controller(SubjectController::class)->group(function () {
            Route::prefix('subjects')->as('subjects.')->group(function () {
                Route::get('/index', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/{subject}/edit', 'edit')->name('edit');
                Route::put('/{subject}', 'update')->name('update');
                Route::delete('/{subject}', 'destroy')->name('destroy');
            });
        });

        Route::controller(TopicController::class)->group(function () {
            Route::prefix('topics')->as('topics.')->group(function () {
                Route::get('/index', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/{topic}/edit', 'edit')->name('edit');
                Route::put('/{topic}', 'update')->name('update');
                Route::delete('/{topic}', 'destroy')->name('destroy');
            });
        });

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});
