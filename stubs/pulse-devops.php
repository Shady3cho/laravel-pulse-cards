<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | Control who can access the DevOps cards. By default, only local
    | environment has access. Override the 'viewPulseDevops' gate for
    | production access control.
    |
    */
    'authorize' => env('PULSE_DEVOPS_ENABLED', true),

    'allowed_emails' => array_filter(explode(',', env('PULSE_DEVOPS_EMAILS', ''))),

    /*
    |--------------------------------------------------------------------------
    | GCP Configuration
    |--------------------------------------------------------------------------
    */
    'gcp' => [
        'project_id' => env('GOOGLE_CLOUD_PROJECT_ID', env('GOOGLE_CLOUD_PROJECT')),
        'region' => env('GOOGLE_CLOUD_REGION', 'us-central1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable individual cards.
    |
    */
    'cards' => [
        'migrations' => true,
        'database_tables' => true,
        'gcp_secrets' => true,
        'cloud_run_jobs' => true,
        'queue_health' => true,
        'environment' => true,
    ],

];
