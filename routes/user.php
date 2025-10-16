<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\TutorialController;
use App\Http\Controllers\User\DownloadController;
use App\Http\Controllers\User\BadgeController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Settings\PasswordController;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Routes pour l'espace utilisateur de la plateforme AutomateHub
| Accessible aux utilisateurs authentifiés
|
*/

Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    
    // Dashboard Utilisateur
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');
    Route::get('/recent-activity', [DashboardController::class, 'getRecentActivity'])->name('recent-activity');
    
    // Tutoriels
    Route::prefix('tutorials')->name('tutorials.')->group(function () {
        Route::get('/', [TutorialController::class, 'index'])->name('index');
        Route::get('/free', [TutorialController::class, 'free'])->name('free');
        Route::get('/premium', [TutorialController::class, 'premium'])->name('premium');
        Route::get('/pro', [TutorialController::class, 'pro'])->name('pro');
        Route::get('/favorites', [TutorialController::class, 'favorites'])->name('favorites');
        Route::get('/history', [TutorialController::class, 'history'])->name('history');
        Route::get('/search', [TutorialController::class, 'search'])->name('search');
        
        // Actions sur les tutoriels
        Route::get('/{tutorial}', [TutorialController::class, 'show'])->name('show');
        Route::post('/{tutorial}/favorite', [TutorialController::class, 'toggleFavorite'])->name('toggle-favorite');
        Route::post('/{tutorial}/complete', [TutorialController::class, 'markAsCompleted'])->name('complete');
        Route::post('/{tutorial}/progress', [TutorialController::class, 'updateProgress'])->name('update-progress');
        Route::get('/{tutorial}/download/{file}', [TutorialController::class, 'downloadFile'])->name('download-file');
        
        // Recommandations
        Route::get('/recommendations/personal', [TutorialController::class, 'recommendations'])->name('recommendations');
        Route::get('/categories/{category}', [TutorialController::class, 'byCategory'])->name('by-category');
        Route::get('/difficulty/{level}', [TutorialController::class, 'byDifficulty'])->name('by-difficulty');
    });
    
    // Téléchargements
    Route::prefix('downloads')->name('downloads.')->group(function () {
        Route::get('/', [DownloadController::class, 'index'])->name('index');
        Route::get('/history', [DownloadController::class, 'history'])->name('history');
        Route::get('/limits', [DownloadController::class, 'limits'])->name('limits');
        Route::post('/{tutorial}', [DownloadController::class, 'download'])->name('download');
        Route::post('/{tutorial}/bulk', [DownloadController::class, 'bulkDownload'])->name('bulk-download');
        Route::post('/{tutorial}/generate-link', [DownloadController::class, 'generateSecureLink'])->name('generate-link');
        Route::get('/secure', [DownloadController::class, 'secureDownload'])->name('secure');
    });
    
    // Badges et Niveau
    Route::prefix('badges')->name('badges.')->group(function () {
        Route::get('/', [BadgeController::class, 'index'])->name('index');
        Route::get('/earned', [BadgeController::class, 'earned'])->name('earned');
        Route::get('/available', [BadgeController::class, 'available'])->name('available');
        Route::get('/progress', [BadgeController::class, 'progress'])->name('progress');
        Route::get('/leaderboard', [BadgeController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/{badge}', [BadgeController::class, 'show'])->name('show');
    });
    
    // Niveau n8n et Quiz
    Route::prefix('level')->name('level.')->group(function () {
        Route::get('/', [BadgeController::class, 'level'])->name('index');
        Route::get('/quiz', [BadgeController::class, 'quiz'])->name('quiz');
        Route::post('/quiz', [BadgeController::class, 'submitQuiz'])->name('quiz.submit');
        Route::get('/progression', [BadgeController::class, 'progression'])->name('progression');
    });
    
    // Profil et Paramètres
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/statistics', [ProfileController::class, 'statistics'])->name('statistics');
        Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');
        
        // Gestion des préférences
        Route::get('/preferences', [ProfileController::class, 'preferences'])->name('preferences');
        Route::post('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences.update');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
        
        // Données personnelles
        Route::get('/export', [ProfileController::class, 'exportData'])->name('export');
        Route::delete('/delete', [ProfileController::class, 'deleteAccount'])->name('delete');
    });
    
    // Abonnement et Facturation
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [ProfileController::class, 'subscription'])->name('index');
        Route::get('/upgrade', [ProfileController::class, 'upgrade'])->name('upgrade');
        Route::post('/upgrade', [ProfileController::class, 'processUpgrade'])->name('upgrade.process');
        Route::get('/billing', [ProfileController::class, 'billing'])->name('billing');
        Route::get('/invoices', [ProfileController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{invoice}/download', [ProfileController::class, 'downloadInvoice'])->name('invoices.download');
        Route::post('/cancel', [ProfileController::class, 'cancelSubscription'])->name('cancel');
    });
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [ProfileController::class, 'notifications'])->name('index');
        Route::post('/{notification}/read', [ProfileController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [ProfileController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [ProfileController::class, 'deleteNotification'])->name('delete');
        Route::get('/settings', [ProfileController::class, 'notificationSettings'])->name('settings');
        Route::post('/settings', [ProfileController::class, 'updateNotificationSettings'])->name('settings.update');
    });
    
    // Paramètres de sécurité
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/', [ProfileController::class, 'security'])->name('index');
        Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
        Route::get('/sessions', [ProfileController::class, 'sessions'])->name('sessions');
        Route::delete('/sessions/{session}', [ProfileController::class, 'revokeSession'])->name('sessions.revoke');
        Route::post('/two-factor/enable', [ProfileController::class, 'enableTwoFactor'])->name('two-factor.enable');
        Route::delete('/two-factor/disable', [ProfileController::class, 'disableTwoFactor'])->name('two-factor.disable');
    });
    
    // Support et Aide
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [ProfileController::class, 'support'])->name('index');
        Route::get('/faq', [ProfileController::class, 'faq'])->name('faq');
        Route::get('/contact', [ProfileController::class, 'contact'])->name('contact');
        Route::post('/contact', [ProfileController::class, 'submitContact'])->name('contact.submit');
        Route::get('/tickets', [ProfileController::class, 'tickets'])->name('tickets');
        Route::get('/tickets/{ticket}', [ProfileController::class, 'showTicket'])->name('tickets.show');
    });
});

// Routes spéciales pour les utilisateurs premium/pro
Route::middleware(['auth', 'verified', 'isPremium'])->prefix('user')->name('user.')->group(function () {
    Route::prefix('premium')->name('premium.')->group(function () {
        Route::get('/tutorials', [TutorialController::class, 'premiumTutorials'])->name('tutorials');
        Route::get('/downloads/unlimited', [DownloadController::class, 'unlimitedDownloads'])->name('downloads.unlimited');
        Route::get('/support/priority', [ProfileController::class, 'prioritySupport'])->name('support.priority');
    });
});
