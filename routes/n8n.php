<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\N8nWorkflowController;

/*
|--------------------------------------------------------------------------
| n8n API Routes
|--------------------------------------------------------------------------
|
| Routes for n8n workflow management integration
|
*/

Route::prefix('api/n8n')->name('n8n.')->middleware(['auth', 'api.rate'])->group(function () {
    
    // Connection test
    Route::get('/test-connection', [N8nWorkflowController::class, 'testConnection'])->name('test');
    
    // Workflow management
    Route::prefix('workflows')->name('workflows.')->group(function () {
        // List all workflows
        Route::get('/', [N8nWorkflowController::class, 'index'])->name('index');
        
        // Create new workflow
        Route::post('/', [N8nWorkflowController::class, 'store'])->name('store');
        
        // Import workflow from file
        Route::post('/import', [N8nWorkflowController::class, 'import'])->name('import');
        
        // Specific workflow operations
        Route::prefix('{workflowId}')->group(function () {
            // Get workflow details
            Route::get('/', [N8nWorkflowController::class, 'show'])->name('show');
            
            // Update workflow
            Route::put('/', [N8nWorkflowController::class, 'update'])->name('update');
            
            // Delete workflow
            Route::delete('/', [N8nWorkflowController::class, 'destroy'])->name('destroy');
            
            // Toggle workflow active state
            Route::post('/toggle', [N8nWorkflowController::class, 'toggle'])->name('toggle');
            
            // Execute workflow
            Route::post('/execute', [N8nWorkflowController::class, 'execute'])->name('execute');
            
            // Get workflow executions
            Route::get('/executions', [N8nWorkflowController::class, 'executions'])->name('executions');
            
            // Export workflow
            Route::post('/export', [N8nWorkflowController::class, 'export'])->name('export');
        });
    });
});

// Admin-only routes
Route::prefix('api/admin/n8n')->name('admin.n8n.')->middleware(['auth', 'is_admin', 'api.rate'])->group(function () {
    
    // All workflow operations without user restrictions
    Route::prefix('workflows')->name('workflows.')->group(function () {
        Route::get('/', [N8nWorkflowController::class, 'index'])->name('index');
        Route::post('/', [N8nWorkflowController::class, 'store'])->name('store');
        Route::post('/import', [N8nWorkflowController::class, 'import'])->name('import');
        
        Route::prefix('{workflowId}')->group(function () {
            Route::get('/', [N8nWorkflowController::class, 'show'])->name('show');
            Route::put('/', [N8nWorkflowController::class, 'update'])->name('update');
            Route::delete('/', [N8nWorkflowController::class, 'destroy'])->name('destroy');
            Route::post('/toggle', [N8nWorkflowController::class, 'toggle'])->name('toggle');
            Route::post('/execute', [N8nWorkflowController::class, 'execute'])->name('execute');
            Route::get('/executions', [N8nWorkflowController::class, 'executions'])->name('executions');
            Route::post('/export', [N8nWorkflowController::class, 'export'])->name('export');
        });
    });
});