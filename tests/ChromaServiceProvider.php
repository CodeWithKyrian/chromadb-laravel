<?php

declare(strict_types=1);

use Codewithkyrian\ChromaDB\ChromaServiceProvider;
use Codewithkyrian\ChromaDB\Client;
use Illuminate\Config\Repository;


it('binds the client on the container', function () {
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

    expect($app->get('chromadb'))->toBeInstanceOf(Client::class);
});

it('binds the client on the container as singleton', function () {
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

    expect($app->get('chromadb'))->toBeInstanceOf(Client::class)
        ->and($app->get('chromadb'))->toBe($app->get('chromadb'));
});