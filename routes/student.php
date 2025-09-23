<?php

use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\User\QuestionController;
use App\Http\Controllers\User\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('pages.student.home');
})->name('student.home');

Route::get('/templates', function () {
    return view('pages.user.templates');
})->name('user.templates');

Route::get('/test', function () {
    return view('pages.student.test');
})->name('student.test');


Route::controller(TestController::class)->group(function () {
    Route::get('/test', 'index')->name('student.test.start');
});

Route::middleware('auth')->group(function () {
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/', [TestController::class, 'index'])->name('home');
        Route::post('/test/start', [TestController::class, 'startTest'])->name('test.start');
        Route::get('/test/{sessionId}', [TestController::class, 'showTest'])->name('test.show');
        Route::post('/test/submit-answer', [TestController::class, 'submitAnswer'])->name('test.submitAnswer');
        Route::post('/test/finish/{sessionId}', [TestController::class, 'finishTest'])->name('test.finish');
        Route::get('/test/result/{sessionId}', [TestController::class, 'showResult'])->name('test.result');
        Route::get('/results', [TestController::class, 'results'])->name('results');
        Route::get('/test/result/{sessionId}', [TestController::class, 'showResult'])->name('test.result');
        Route::delete('/test/result/{sessionId}', [TestController::class, 'deleteResult'])->name('test.result.delete');
        Route::get('/test/statistics', [TestController::class, 'getTestStatistics'])->name('test.statistics');
    });
});
