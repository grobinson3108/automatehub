<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\Auth\GoogleAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes (Public)
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Test route
Route::get('/test', function () {
    return view('test');
});

// Test Inertia
Route::get('/test-inertia', function () {
    return \Inertia\Inertia::render('Test');
});

// Dashboard système (accès temporaire)
Route::get('/system-dashboard', function () {
    $controller = new \App\Http\Controllers\Admin\SystemStatusController();
    return $controller->index();
})->name('system-dashboard');

// Public routes
Route::get('/', function () {
    return \Inertia\Inertia::render('Welcome');
})->name('home');

// Tutorials page
Route::get('/tutorials', function () {
    return view('tutorials');
})->name('tutorials');

// Workflows routes (using Inertia)
Route::get('/workflows', [App\Http\Controllers\WorkflowController::class, 'index'])->name('workflows.index');

Route::prefix('workflows')->name('workflows.')->group(function () {
    Route::get('/{workflow}', [App\Http\Controllers\WorkflowController::class, 'show'])->name('show');

    Route::middleware(['auth'])->group(function () {
        Route::get('/{workflow}/download', [App\Http\Controllers\WorkflowController::class, 'download'])->name('download');
        Route::get('/{workflow}/import-url', [App\Http\Controllers\WorkflowController::class, 'getImportUrl'])->name('import-url');
        Route::get('/{workflow}/preview', [App\Http\Controllers\WorkflowController::class, 'preview'])->name('preview');
    });
});

// Public tutorials - Removed duplicate route, using Inertia version above

// Blog routes
Route::get('/blog', function () {
    return view('blog');
})->name('blog.index');

Route::get('/blog/{slug}', [App\Http\Controllers\Frontend\BlogController::class, 'show'])->name('blog.show');

// About page
Route::get('/about', function () {
    return view('about');
})->name('about');

// Contact page and form submission
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Downloads page
Route::get('/downloads', function () {
    return view('downloads');
})->name('downloads');

// Privacy page
Route::get('/privacy', function () {
    return view('legal.privacy-policy');
})->name('privacy-policy');

// Legal page
Route::get('/legal', function () {
    return view('legal.terms');
})->name('legal');

// Pricing page
Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

// Pack routes
Route::get('/packs', [App\Http\Controllers\PackController::class, 'index'])->name('packs.index');
Route::get('/packs/{slug}', [App\Http\Controllers\PackController::class, 'show'])->name('packs.show');
Route::post('/packs/{slug}/checkout', [App\Http\Controllers\PackController::class, 'checkout'])->name('packs.checkout');

// Dashboard redirect
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Quiz routes
    Route::prefix('quiz')->name('quiz.')->group(function () {
        Route::get('/questions', [App\Http\Controllers\QuizController::class, 'getQuestions'])->name('questions');
        Route::post('/submit', [App\Http\Controllers\QuizController::class, 'submitAnswers'])->name('submit');
    });
});

// Onboarding routes
Route::middleware(['auth'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/welcome', [App\Http\Controllers\OnboardingController::class, 'welcome'])->name('welcome');
    Route::post('/level', [App\Http\Controllers\OnboardingController::class, 'updateLevel'])->name('update-level');
    Route::get('/preferences', [App\Http\Controllers\OnboardingController::class, 'preferences'])->name('preferences');
    Route::post('/preferences', [App\Http\Controllers\OnboardingController::class, 'updatePreferences'])->name('update-preferences');
});

require __DIR__.'/settings.php';
// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Cookie Consent Routes
Route::post('/api/cookie-consent', [CookieConsentController::class, 'store'])->name('cookie-consent.store');
Route::get('/cookie-preferences', [CookieConsentController::class, 'preferences'])->name('cookie-preferences');

// Google OAuth Routes
Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Blog API endpoints for n8n integration
Route::prefix('api')->name('api.')->group(function () {
    Route::post('/posts', [App\Http\Controllers\Api\BlogApiController::class, 'store'])->name('posts.store');
    Route::put('/posts/{slug}', [App\Http\Controllers\Api\BlogApiController::class, 'update'])->name('posts.update');
});

// Include route groups
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/user.php';
require __DIR__.'/n8n.php';
require __DIR__.'/api-marketplace.php';
