<?php

use App\Http\Controllers\AIQuestionController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GamePlayController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('games.index');
    })->name('dashboard');

    // Game Management Routes
    Route::resource('games', GameController::class);

    // Subscription Routes
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
        Route::get('/billing-portal', [SubscriptionController::class, 'billingPortal'])->name('billing-portal');
    });

    // AI Question Generation Routes
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::post('/generate', [AIQuestionController::class, 'generate'])->name('generate');
        Route::get('/remaining', [AIQuestionController::class, 'remaining'])->name('remaining');
    });

    // Game Play Routes (Teacher)
    Route::prefix('game-play')->name('game-play.')->group(function () {
        Route::post('/start/{game}', [GamePlayController::class, 'start'])->name('start');
        Route::get('/teacher/{session}', [GamePlayController::class, 'teacher'])->name('teacher');
        Route::post('/end/{session}', [GamePlayController::class, 'end'])->name('end');
    });
});

// Public Game Play Routes (No auth required)
Route::prefix('play')->name('play.')->group(function () {
    Route::get('/presentation/{session}', [GamePlayController::class, 'presentation'])->name('presentation');
    Route::get('/team', [GamePlayController::class, 'team'])->name('team');
});
