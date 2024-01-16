<?php

declare(strict_types=1);

namespace Codewithkyrian\ChromaDB\Tests;

use Codewithkyrian\ChromaDB\ChromaServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithMigration;
use function Orchestra\Testbench\workbench_path;

#[WithMigration('laravel', 'job')]
class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            ChromaServiceProvider::class
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(workbench_path('database/migrations'));
    }
}