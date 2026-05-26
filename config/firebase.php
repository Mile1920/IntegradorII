<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase services. You can find your project ID and
    | credentials in the Firebase console.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID', 'mina-porco-sensores'),

    'database_url' => env('FIREBASE_DATABASE_URL', 'https://mina-porco-sensores-default-rtdb.asia-southeast1.firebasedatabase.app/'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Path to the service account JSON file. You can download this from the
    | Firebase console under Project Settings > Service Accounts.
    |
    */

    'credentials' => env('FIREBASE_CREDENTIALS', storage_path('firebase_credentials.json')),

    /*
    |--------------------------------------------------------------------------
    | Default Storage Bucket
    |--------------------------------------------------------------------------
    |
    | The default Firebase Storage bucket to use.
    |
    */

    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
];