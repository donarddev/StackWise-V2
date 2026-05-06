<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/recommendation', [RecommendationController::class, 'index'])->name('recommendation.index');
Route::post('/recommendation', [RecommendationController::class, 'generate'])->name('recommendation.generate');
Route::get('/recommendation/history', [RecommendationController::class, 'history'])->name('recommendation.history');
Route::get('/recommendation/{recommendation}', [RecommendationController::class, 'show'])->name('recommendation.show');

Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');

Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot', [ChatbotController::class, 'send'])->name('chatbot.send');
Route::post('/chatbot/clear', [ChatbotController::class, 'clear'])->name('chatbot.clear');

Route::get('/about', [AboutController::class, 'index'])->name('about');
