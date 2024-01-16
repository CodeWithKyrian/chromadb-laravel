<?php

declare(strict_types=1);

use Codewithkyrian\ChromaDB\ChromaServiceProvider;
use Codewithkyrian\ChromaDB\Client;
use Illuminate\Config\Repository;


it('binds the client on the container', function () {
    expect(app()->get('chromadb'))->toBeInstanceOf(Client::class);
});

it('binds the client on the container as singleton', function () {
    expect(app()->get('chromadb'))->toBeInstanceOf(Client::class)
        ->and(app()->get('chromadb'))->toBe(app()->get('chromadb'));
});