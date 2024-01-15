<?php

declare(strict_types=1);


namespace Codewithkyrian\ChromaDB\Contracts;

use Codewithkyrian\ChromaDB\Resources\CollectionResource;

interface ChromaModel
{
    public static function getChromaCollection(): CollectionResource;

    public function collectionName(): string;

    public function embeddingFunction(): string;

    function metadataFields(): array;

    function documentFields(): array;

    public function generateMetadata(): array;

    public function generateChromaDocument(): array;

}