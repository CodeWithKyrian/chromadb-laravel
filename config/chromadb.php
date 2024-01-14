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
];

