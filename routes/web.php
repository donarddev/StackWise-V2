<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');

Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot/send', [ChatbotController::class, 'send'])->name('chatbot.send');
Route::post('/chatbot/clear', [ChatbotController::class, 'clear'])->name('chatbot.clear');

Route::get('/about', [AboutController::class, 'index'])->name('about');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/recommendation/create', [RecommendationController::class, 'index'])->name('recommendation.create');
    Route::get('/recommendation', [RecommendationController::class, 'index'])->name('recommendation.index');
    Route::post('/recommendation/generate', [RecommendationController::class, 'generate'])->name('recommendation.generate');
    Route::get('/recommendation/history', [RecommendationController::class, 'history'])->name('recommendation.history');
    Route::get('/recommendation/{recommendation}', [RecommendationController::class, 'show'])
        ->whereNumber('recommendation')
        ->name('recommendation.show');

    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
