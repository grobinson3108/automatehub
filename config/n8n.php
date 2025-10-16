<?php

return [

    /*
    |--------------------------------------------------------------------------
    | n8n Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for n8n integration with AutomateHub.
    | These settings are used to connect to the n8n instance.
    |
    */

    'url' => env('N8N_URL', 'https://n8n.automatehub.fr'),
    
    'api_key' => env('N8N_API_KEY'),
    
    'timeout' => env('N8N_TIMEOUT', 30),
    
    'verify_ssl' => env('N8N_VERIFY_SSL', true),
    
    'export_path' => env('N8N_EXPORT_PATH', storage_path('app/tutorials/n8n-exports')),
    
    'max_retries' => env('N8N_MAX_RETRIES', 3),
    
    'retry_delay' => env('N8N_RETRY_DELAY', 1000), // milliseconds

];