<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TweetController;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Controllers\WebhookController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
})->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);

Route::middleware('auth')->group(function () {
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.show');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::post('/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
    Route::post('/billing/resume', [BillingController::class, 'resume'])->name('billing.resume');
    Route::post('/billing/portal', [BillingController::class, 'portal'])->name('billing.portal');
    Route::get('/billing/success', [BillingController::class, 'success'])->name('billing.success');

    Route::middleware('subscribed')->group(function () {
        Route::get('/dashboard', [PostController::class, 'index'])->name('dashboard');
        Route::resource('posts', PostController::class)->except(['index']);

        Route::post('posts/{post}/archive', [PostController::class, 'archive'])->name('posts.archive');
        Route::post('posts/{post}/restore', [PostController::class, 'restore'])->name('posts.restore');

        Route::post('posts/{post}/tweets/generate', [TweetController::class, 'generate'])->name('posts.tweets.generate');
        Route::post('posts/{post}/tweets/regenerate', [TweetController::class, 'regenerate'])->name('posts.tweets.regenerate');

        Route::patch('tweets/{tweet}', [TweetController::class, 'update'])->name('tweets.update');
        Route::post('tweets/{tweet}/mark-posted', [TweetController::class, 'markPosted'])->name('tweets.mark-posted');
        Route::post('tweets/{tweet}/discard', [TweetController::class, 'discard'])->name('tweets.discard');
        Route::post('tweets/{tweet}/restore', [TweetController::class, 'restore'])->name('tweets.restore');
    });
});
