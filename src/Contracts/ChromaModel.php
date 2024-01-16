<?php

declare(strict_types=1);

namespace Codewithkyrian\ChromaDB\Contracts;

use Codewithkyrian\ChromaDB\Embeddings\EmbeddingFunction;
use Codewithkyrian\ChromaDB\Resources\CollectionResource;

interface ChromaModel
{

    public function collectionName(): string;

    public function embeddingFunction(): ?EmbeddingFunction;

    function metadataFields(): array;

    function documentFields(): array;

    public function generateMetadata(): array;

    public function generateChromaDocument(): string;

    public static function getChromaCollection(): CollectionResource;

}