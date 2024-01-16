<?php

declare(strict_types=1);

namespace Codewithkyrian\ChromaDB\Tests;

use Codewithkyrian\ChromaDB\ChromaServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ChromaServiceProvider::class
        ];
    }
}