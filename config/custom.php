<?php

return [
    
    'simulate_loading' => false && env('APP_ENV') === 'local',
    
    'app_admin_email' => env('APP_ADMIN_EMAIL'),
    
    'hide_current_organ_builders_importance' => true,
    
    'show_donate_alert' => env('SHOW_DONATE_ALERT', false),

    'ai' => [
        'max_response_length' => env('AI_MAX_RESPONSE_LENGTH', 6000),
        'retry_attempts' => env('AI_RETRY_ATTEMPTS', 2),
        'retry_sleep_ms' => env('AI_RETRY_SLEEP_MS', 500),
    ],
    
];
