<?php

declare(strict_types=1);

use Codewithkyrian\ChromaDB\Facades\ChromaDB;
use Codewithkyrian\ChromaDB\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(fn() => ChromaDB::deleteAllCollections())
    ->in(__DIR__);

