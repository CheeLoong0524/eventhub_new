<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Firebase project credentials. You can find these in your Firebase console
    | under Project Settings > Service Accounts.
    |
    */

    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS_FILE', null),
        'project_id' => env('FIREBASE_PROJECT_ID', 'eventhubi1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | Your Firebase project ID.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID', 'eventhubi1'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Database URL
    |--------------------------------------------------------------------------
    |
    | Your Firebase Realtime Database URL (optional).
    |
    */

    'database_url' => env('FIREBASE_DATABASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Storage Bucket
    |--------------------------------------------------------------------------
    |
    | Your Firebase Storage bucket (optional).
    |
    */

    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Auth Emulator Host
    |--------------------------------------------------------------------------
    |
    | Firebase Auth emulator host for local development (optional).
    |
    */

    'auth_emulator_host' => env('FIREBASE_AUTH_EMULATOR_HOST'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Auth Emulator Port
    |--------------------------------------------------------------------------
    |
    | Firebase Auth emulator port for local development (optional).
    |
    */

    'auth_emulator_port' => env('FIREBASE_AUTH_EMULATOR_PORT'),
];
