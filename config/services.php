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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'timeout' => (int) env('OPENAI_TIMEOUT', 30),
        'temperature' => (float) env('OPENAI_TEMPERATURE', 0.5),
        'max_tokens' => (int) env('OPENAI_MAX_TOKENS', 600),
    ],

    'deepseek' => [
        'key' => env('DEEPSEEK_API_KEY'),
        'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
        'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com/v1'),
        'timeout' => (int) env('DEEPSEEK_TIMEOUT', 30),
        'temperature' => (float) env('DEEPSEEK_TEMPERATURE', 0.5),
        'max_tokens' => (int) env('DEEPSEEK_MAX_TOKENS', 600),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        // Bascules secondaires pour contourner le quota gratuit (20 req/jour/projet):
        // la 1re clé est utilisée d'abord, les suivantes ne servent qu'en secours
        // quand une clé atteint son quota quotidien (429 quota épuisé).
        'keys' => array_values(array_filter([
            env('GEMINI_API_KEY'),
            env('GEMINI_API_KEY_2'),
        ])),
        'model' => env('GEMINI_MODEL', 'gemini-3.6-flash'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta/openai'),
        'timeout' => (int) env('GEMINI_TIMEOUT', 30),
        'temperature' => (float) env('GEMINI_TEMPERATURE', 0.5),
        'max_tokens' => (int) env('GEMINI_MAX_TOKENS', 600),
    ],

    'chatbot' => [
        'provider' => env('CHATBOT_PROVIDER', 'openai'),
    ],

    // Recherche web Google via Serper.dev (outil `recherche_web` du chatbot).
    'search' => [
        'key' => env('SERPER_API_KEY'),
    ],

];
