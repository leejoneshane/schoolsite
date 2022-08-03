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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/login/facebook/callback',
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/login/google/callback',
    ],

    'yahoo' => [
        'client_id'     => env('YAHOO_CLIENT_ID'),
        'client_secret' => env('YAHOO_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/login/yahoo/callback',
    ],

    'line' => [
        'client_id'     => env('LINE_CLIENT_ID'),
        'client_secret' => env('LINE_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/login/line/callback',
    ],

    'tpedu' => [
        'expired_days' => 30,
        'server' => 'https://ldap.tp.edu.tw',
        'app' => env('TPEDU_APP'),
        'secret' => env('TPEDU_SECRET'),
        'callback' => env('TPEDU_CALLBACK'),
        'token' => env('TPEDU_TOKEN'),
        'school' => env('SCHOOL'),
        'base_unit' => '科任,級任',
        'endpoint' => [
            'token' => 'oauth/token',
            'login' => 'oauth/authorize',
            'user' => 'api/v2/user',
            'profile' => 'api/v2/profile',
            'school' => 'api/v2/school/{school}',
            'all_teachers' => 'api/v2/school/{school}/teachers',
            'all_units' => 'api/v2/school/{school}/ou',
            'one_unit' => 'api/v2/school/{school}/ou/{unit}',
            'teachers_of_unit' => 'api/v2/school/{school}/ou/{unit}/teachers',
            'roles_of_unit' => 'api/v2/school/{school}/ou/{unit}/role',
            'one_role' => 'api/v2/school/{school}/ou/{unit}/role/{role}',
            'teachers_of_role' => 'api/v2/school/{school}/ou/{unit}/role/{role}/teachers',
            'all_classes' => 'api/v2/school/{school}/class',
            'one_class' => 'api/v2/school/{school}/class/{class}',
            'teachers_of_class' => 'api/v2/school/{school}/class/{class}/teachers',
            'students_of_class' => 'api/v2/school/{school}/class/{class}/students',
            'subjects_of_class' => 'api/v2/school/{school}/class/{class}/subjects',
            'all_subjects' => 'api/v2/school/{school}/subject',
            'one_subject' => 'api/v2/school/{school}/subject/{subject}',
            'teachers_of_subject' => 'api/v2/school/{school}/subject/{subject}/teachers',
            'classes_of_subject' => 'api/v2/school/{school}/subject/{subject}/classes',
            'find_users' => 'api/v2/school/{school}/people',
            'one_user' => 'api/v2/school/{school}/people/{uuid}',            
        ],
    ],

    'ad' => [
        'server' => env('AD_HOST'),
        'port' => env('AD_PORT', 636),
        'admin' => env('AD_ADMIN', 'administrator'),
        'password' => env('AD_PASS'),
        'base_dn' => env('AD_BASE_DN'),
        'users_dn' => env('AD_USERS_DN'),
        'ca_file' => env('AD_CA_FILE'),
    ],

    'gsuite' => [
        'domain' => env('GOOGLE_DOMAIN'),
        'auth_config' => env('GOOGLE_AUTH_CONFIG'),
        'admin' => env('GOOGLE_ADMIN'),
        'calendar' => env('GOOGLE_CALENDAR_OWNER'),
        'student_orgunit' => env('GOOGLE_STUDENT_ORGUNIT'),
        'teacher_orgunit' => env('GOOGLE_TEACHER_ORGUNIT'),
    ],

];
