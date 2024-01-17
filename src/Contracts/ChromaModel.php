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

    public function toChromaMetadata(): array;

    public function toChromaDocument(): string;

    public static function getChromaCollection(): CollectionResource;

}