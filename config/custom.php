<?php

return [
    
    'simulate_loading' => false && env('APP_ENV') === 'local',
    
    'app_admin_email' => env('APP_ADMIN_EMAIL'),
    
    'hide_current_organ_builders_importance' => true,
    
    'show_donate_alert' => true,
    
];
