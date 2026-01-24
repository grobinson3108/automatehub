<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\TutorialManagementController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SystemStatusController;
use App\Http\Controllers\Admin\VideoContentController;
use App\Http\Controllers\Admin\VideoIdeasController;
use App\Http\Controllers\Admin\PublicationCalendarController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes pour l'administration de la plateforme AutomateHub
| Accessible uniquement aux administrateurs
|
*/

Route::middleware(['auth', 'verified', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Test simple
    Route::get('/test-simple', function() {
        return '<h1>Test Admin Simple</h1><p>Utilisateur: ' . auth()->user()->email . '</p><p>Admin: ' . (auth()->user()->is_admin ? 'Oui' : 'Non') . '</p>';
    })->name('test-simple');
    Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');
    Route::get('/recent-activity', [DashboardController::class, 'getRecentActivity'])->name('recent-activity');
    
    // Dashboard Système
    Route::get('/system-status', [SystemStatusController::class, 'index'])->name('system-status');
    
    // Gestion des Utilisateurs
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-subscription', [UserManagementController::class, 'toggleSubscription'])->name('toggle-subscription');
        Route::get('/export/csv', [UserManagementController::class, 'export'])->name('export');
        Route::post('/bulk-action', [UserManagementController::class, 'bulkAction'])->name('bulk-action');
        
        // Sous-sections utilisateurs
        Route::get('/subscriptions/overview', [UserManagementController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/activity/logs', [UserManagementController::class, 'activity'])->name('activity');
        Route::get('/levels/n8n', [UserManagementController::class, 'n8nLevels'])->name('levels');
    });
    
    // Gestion des Tutoriels
    Route::prefix('tutorials')->name('tutorials.')->group(function () {
        Route::get('/', [TutorialManagementController::class, 'index'])->name('index');
        Route::get('/create', [TutorialManagementController::class, 'create'])->name('create');
        Route::post('/', [TutorialManagementController::class, 'store'])->name('store');
        Route::get('/{tutorial}', [TutorialManagementController::class, 'show'])->name('show');
        Route::get('/{tutorial}/edit', [TutorialManagementController::class, 'edit'])->name('edit');
        Route::put('/{tutorial}', [TutorialManagementController::class, 'update'])->name('update');
        Route::delete('/{tutorial}', [TutorialManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{tutorial}/toggle-publish', [TutorialManagementController::class, 'togglePublish'])->name('toggle-publish');
        Route::post('/upload-files', [TutorialManagementController::class, 'uploadFiles'])->name('upload-files');
        
        // Gestion du contenu
        Route::get('/categories/manage', [TutorialManagementController::class, 'categories'])->name('categories');
        Route::get('/tags/manage', [TutorialManagementController::class, 'tags'])->name('tags');
        Route::get('/files/manage', [TutorialManagementController::class, 'files'])->name('files');
        Route::post('/files/upload', [TutorialManagementController::class, 'uploadFilesToTutorial'])->name('files.upload');
        Route::get('/files/{tutorialId}/{filename}', [TutorialManagementController::class, 'showFile'])->name('files.show');
        Route::get('/files/{tutorialId}/{filename}/download', [TutorialManagementController::class, 'downloadFile'])->name('files.download');
        Route::delete('/files/{tutorialId}/{filename}', [TutorialManagementController::class, 'deleteFile'])->name('files.delete');
    });
    
    // Gestion du Blog
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [TutorialManagementController::class, 'blogIndex'])->name('index');
        Route::get('/create', [TutorialManagementController::class, 'blogCreate'])->name('create');
        Route::post('/', [TutorialManagementController::class, 'blogStore'])->name('store');
        Route::get('/{post}/edit', [TutorialManagementController::class, 'blogEdit'])->name('edit');
        Route::put('/{post}', [TutorialManagementController::class, 'blogUpdate'])->name('update');
        Route::delete('/{post}', [TutorialManagementController::class, 'blogDestroy'])->name('destroy');
    });
    
    // Analytics et Rapports
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AnalyticsController::class, 'users'])->name('users');
        Route::get('/content', [AnalyticsController::class, 'content'])->name('content');
        Route::get('/conversions', [AnalyticsController::class, 'conversions'])->name('conversions');
        Route::get('/revenue', [AnalyticsController::class, 'revenue'])->name('revenue');
        Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
    });
    
    // Finances
    Route::prefix('finances')->name('finances.')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'financeDashboard'])->name('dashboard');
        Route::get('/transactions', [AnalyticsController::class, 'transactions'])->name('transactions');
        Route::get('/invoices', [AnalyticsController::class, 'invoices'])->name('invoices');
        Route::get('/reports', [AnalyticsController::class, 'reports'])->name('reports');
    });
    
    // Messages de Contact
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [UserManagementController::class, 'contacts'])->name('index');
        Route::get('/{contact}', [UserManagementController::class, 'showContact'])->name('show');
        Route::post('/{contact}/reply', [UserManagementController::class, 'replyContact'])->name('reply');
        Route::delete('/{contact}', [UserManagementController::class, 'deleteContact'])->name('destroy');
    });
    
    // REMOVED: n8n MasterClass (legacy)


    // Gestion du Contenu Vidéo
    Route::prefix('video-content')->name('video-content.')->group(function () {
        Route::get('/', [VideoContentController::class, 'index'])->name('index');
        Route::get('/create', [VideoContentController::class, 'create'])->name('create');
        Route::post('/', [VideoContentController::class, 'store'])->name('store');
        Route::get('/{videoContentPlan}', [VideoContentController::class, 'show'])->name('show');
        Route::get('/{videoContentPlan}/edit', [VideoContentController::class, 'edit'])->name('edit');
        Route::put('/{videoContentPlan}', [VideoContentController::class, 'update'])->name('update');
        Route::delete('/{videoContentPlan}', [VideoContentController::class, 'destroy'])->name('destroy');
        Route::post('/{videoContentPlan}/mark-as-done', [VideoContentController::class, 'markAsDone'])->name('mark-as-done');
        Route::post('/{videoContentPlan}/mark-as-in-progress', [VideoContentController::class, 'markAsInProgress'])->name('mark-as-in-progress');
        Route::post('/update-priority', [VideoContentController::class, 'updatePriority'])->name('update-priority');
        // REMOVED: generateFromWorkflows (legacy n8n)
    });

    // Gestion des Idées Vidéos
    Route::prefix('video-ideas')->name('video-ideas.')->group(function () {
        Route::get('/', [VideoIdeasController::class, 'index'])->name('index');
        Route::get('/{videoIdea}', [VideoIdeasController::class, 'show'])->name('show');
        Route::get('/{videoIdea}/edit', [VideoIdeasController::class, 'edit'])->name('edit');
        Route::put('/{videoIdea}', [VideoIdeasController::class, 'update'])->name('update');

        // Génération de planning
        Route::post('/generate-schedule/{videoContentPlan}', [VideoIdeasController::class, 'generateSchedule'])->name('generate-schedule');
        Route::post('/generate-all-schedules', [VideoIdeasController::class, 'generateAllSchedules'])->name('generate-all-schedules');

        // Tâches quotidiennes
        Route::get('/daily-tasks/{date?}', [VideoIdeasController::class, 'dailyTasks'])->name('daily-tasks');

        // AJAX Routes pour édition temps réel
        Route::patch('/{videoIdea}/update-filming-date', [VideoIdeasController::class, 'updateFilmingDate'])->name('update-filming-date');
        Route::patch('/{videoIdea}/update-filming-time', [VideoIdeasController::class, 'updateFilmingTime'])->name('update-filming-time');
        Route::get('/{videoIdea}/check-conflicts', [VideoIdeasController::class, 'checkConflicts'])->name('check-conflicts');
        Route::get('/available-slots/{date}', [VideoIdeasController::class, 'getAvailableSlots'])->name('available-slots');
    });

    // Calendrier de Publications
    Route::prefix('publication-calendar')->name('publication-calendar.')->group(function () {
        Route::get('/', [PublicationCalendarController::class, 'index'])->name('index');
        Route::get('/today', [PublicationCalendarController::class, 'today'])->name('today');
        Route::get('/export', [PublicationCalendarController::class, 'exportCalendar'])->name('export');
        Route::get('/json', [PublicationCalendarController::class, 'getPublicationsJson'])->name('json');

        Route::post('/generate/{videoContentPlan}', [PublicationCalendarController::class, 'generateSchedule'])->name('generate');
        Route::post('/generate-all', [PublicationCalendarController::class, 'generateAllSchedules'])->name('generate-all');

        Route::get('/{publication}', [PublicationCalendarController::class, 'show'])->name('show');
        Route::get('/{publication}/edit', [PublicationCalendarController::class, 'edit'])->name('edit');
        Route::put('/{publication}', [PublicationCalendarController::class, 'update'])->name('update');
        Route::post('/{publication}/duplicate', [PublicationCalendarController::class, 'duplicate'])->name('duplicate');

        Route::patch('/{publication}/status', [PublicationCalendarController::class, 'updateStatus'])->name('update-status');
        Route::post('/{publication}/mark-published', [PublicationCalendarController::class, 'markAsPublished'])->name('mark-published');
        Route::post('/{publication}/update-metrics', [PublicationCalendarController::class, 'updateMetrics'])->name('update-metrics');
    });

    // Outils d'Administration
    Route::prefix('tools')->name('tools.')->group(function () {
        // TODO V2: Ajouter outils admin pour mini-apps
    });

    // Paramètres Système
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [DashboardController::class, 'settings'])->name('index');
        Route::post('/update', [DashboardController::class, 'updateSettings'])->name('update');
        Route::get('/cache/clear', [DashboardController::class, 'clearCache'])->name('cache.clear');
        Route::get('/logs', [DashboardController::class, 'logs'])->name('logs');
    });
});
