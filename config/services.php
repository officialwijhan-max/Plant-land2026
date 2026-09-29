<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Disclosed licensing gate - see App\Services\LicenseService and
    // docs/LICENSING.md. Leaving endpoint/app_id/token/secret unset
    // disables the check entirely (the app runs normally, ungated).
    'license' => [
        'endpoint' => env('LICENSE_ENDPOINT'),
        'app_id' => env('LICENSE_APP_ID'),
        'token' => env('LICENSE_TOKEN'),
        'secret' => env('LICENSE_SECRET'),
        // Per-deployment emergency override hash - see
        // App\Console\Commands\GenerateLicenseOverrideCode and
        // App\Console\Commands\LicenseOverride.
        'override_hash' => env('LICENSE_OVERRIDE_HASH'),
    ],

];
