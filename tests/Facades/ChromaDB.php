<?php

declare(strict_types=1);

use Codewithkyrian\ChromaDB\ChromaServiceProvider;
use Codewithkyrian\ChromaDB\Facades\ChromaDB;
use Illuminate\Config\Repository;

it('resolves and calls the methods', function () {
    $app = app();

    $app->bind('config', fn() => new Repository([
        'chromadb' => [
            'host' => 'http://localhost',
            'port' => 8000,
            'database' => 'default_database',
            'tenant' => 'default_tenant',
        ],
    ]));

    (new ChromaServiceProvider($app))->register();

    ChromaDB::setFacadeApplication($app);

    $version = ChromaDB::version();

    expect($version)
        ->toBeString()
        ->toMatch('/^[0-9]+\.[0-9]+\.[0-9]+$/');
});

