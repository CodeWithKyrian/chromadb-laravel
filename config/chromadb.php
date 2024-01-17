<?php

declare(strict_types=1);

return [
    /*
     |--------------------------------------------------------------------------
     | ChromaDB Host
     |--------------------------------------------------------------------------
     |
     | Here you may specify your ChromaDB Host. This is the host where your ChromaDB
     | instance is running. This is used to connect to your ChromaDB instance.
     */
    'host' => env('CHROMA_HOST', 'localhost'),

    /*
     |--------------------------------------------------------------------------
     | ChromaDB Port
     |--------------------------------------------------------------------------
     |
     | Here you may specify your ChromaDB Port. This is the port where your ChromaDB
     | instance is running. This is used to connect to your ChromaDB instance.
     */
    'port' => env('CHROMA_PORT', 8000),

    /*
     |--------------------------------------------------------------------------
     | ChromaDB Tenant
     |--------------------------------------------------------------------------
     |
     | This is the tenant that you want to connect to.
     */
    'tenant' => env('CHROMA_TENANT', 'default_tenant'),

    /*
     |--------------------------------------------------------------------------
     | ChromaDB Database
     |--------------------------------------------------------------------------
     |
     | This is the database that you want to connect to.
     */
    'database' => env('CHROMA_DATABASE', 'default_database'),


    /*
     |--------------------------------------------------------------------------
     | ChromaDB Sync
     |--------------------------------------------------------------------------
     |
     | This is the configuration for the ChromaDB Sync feature. This feature
     | allows you to sync data from your local database to your ChromaDB
     | instance.
     */
    'sync' => [

        /*
         |--------------------------------------------------------------------------
         | ChromaDB Sync Enabled
         |--------------------------------------------------------------------------
         |
         | This option controls whether the ChromaDB Sync feature is enabled. If
         | this is set to false, then the ChromaDB Sync feature will not be
         | enabled.
         */
        'enabled' => env('CHROMA_SYNC_ENABLED', true),

        /*
         |--------------------------------------------------------------------------
         | ChromaDB Sync Queue
         |--------------------------------------------------------------------------
         |
         | This option controls which queue the ChromaDB Sync feature will use.
         | This is used to queue the sync jobs. Set to false to disable queueing
         | and run the sync jobs immediately.
         */
        'queue' => env('CHROMA_SYNC_QUEUE', 'default'),

        /*
         |--------------------------------------------------------------------------
         | ChromaDB Sync Connection
         |--------------------------------------------------------------------------
         |
         | This option controls which connection the ChromaDB Sync feature will use.
         | This is used to queue the sync jobs.
         */
        'connection' => env('CHROMA_SYNC_CONNECTION', 'database'),

        /*
         |--------------------------------------------------------------------------
         | ChromaDB Sync Tries
         |--------------------------------------------------------------------------
         |
         | This option controls how many times the Job will be retried if it fails
         | while trying to sync the data to ChromaDB.
         */
        'tries' => env('CHROMA_SYNC_TRIES', 3),

    ],
];

