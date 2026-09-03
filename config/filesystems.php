<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Disque "documents" — fichiers importés (Excel, Word, PDF)
        |--------------------------------------------------------------------------
        |
        | Disque dédié aux documents : en local le contenu vit dans
        | storage/app/private/documents ; en production on bascule vers un
        | stockage objet (S3, MinIO, R2, Backblaze…) en ne modifiant QUE la
        | variable DOCUMENT_DISK dans l'environnement (ex. DOCUMENT_DISK=s3).
        | Le contrôleur DocumentController utilise ce disque (driver-agnostique),
        | les routes de prévisualisation/téléchargement restent inchangées.
        |
        */
        'documents' => [
            'driver' => env('DOCUMENT_DISK', 'local'), // bascule : local → s3
            // Local (ignoré par les autres drivers)
            'root' => storage_path('app/private/documents'),
            // Paramètres S3-compatible (S3, MinIO, R2…) — ignorés en local
            'key' => env('DOCUMENT_AWS_ACCESS_KEY_ID'),
            'secret' => env('DOCUMENT_AWS_SECRET_ACCESS_KEY'),
            'region' => env('DOCUMENT_AWS_DEFAULT_REGION'),
            'bucket' => env('DOCUMENT_AWS_BUCKET'),
            'url' => env('DOCUMENT_AWS_URL'),
            'endpoint' => env('DOCUMENT_AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('DOCUMENT_AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Disque "photos" — photos des infrastructures
        |--------------------------------------------------------------------------
        |
        | Même principe que le disque documents : contenu privé en local
        | (storage/app/private/photos), bascule vers un stockage objet
        | (S3, MinIO, R2…) via la seule variable PHOTO_DISK. Les images ne
        | sont servies que par la route /Photos/{photo} (aucun accès direct).
        |
        */
        'photos' => [
            'driver' => env('PHOTO_DISK', 'local'), // bascule : local → s3
            'root' => storage_path('app/private/photos'),
            'key' => env('PHOTO_AWS_ACCESS_KEY_ID'),
            'secret' => env('PHOTO_AWS_SECRET_ACCESS_KEY'),
            'region' => env('PHOTO_AWS_DEFAULT_REGION'),
            'bucket' => env('PHOTO_AWS_BUCKET'),
            'url' => env('PHOTO_AWS_URL'),
            'endpoint' => env('PHOTO_AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('PHOTO_AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
