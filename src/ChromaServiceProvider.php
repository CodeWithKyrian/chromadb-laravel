<?php

declare(strict_types=1);


namespace Codewithkyrian\ChromaDB;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * @internal
 */
class ChromaServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/chromadb.php' => config_path('chromadb.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/chromadb.php', 'chromadb');

        $this->app->singleton('chromadb', function () {
            return ChromaDB::factory()
                ->withHost(config('chromadb.host'))
                ->withPort(config('chromadb.port'))
                ->withDatabase(config('chromadb.database'))
                ->withTenant(config('chromadb.tenant'))
                ->connect();
        });
    }

    public function provides(): array
    {
        return ['chromadb'];
    }

    public function defers() : array
    {
        return ['chromadb'];
    }
}