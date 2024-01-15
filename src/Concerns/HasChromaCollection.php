<?php

declare(strict_types=1);


namespace Codewithkyrian\ChromaDB\Concerns;

use Codewithkyrian\ChromaDB\Embeddings\EmbeddingFunction;
use Codewithkyrian\ChromaDB\Facades\ChromaDB;
use Codewithkyrian\ChromaDB\Jobs\UpdateChromaCollectionJob;
use Codewithkyrian\ChromaDB\Resources\CollectionResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method mixed getKey()
 * @method string getTable()
 * @method mixed getAttribute(string $field)
 * @method self saved(\Illuminate\Events\QueuedClosure|\Closure|string|array $callback)
 * @method self deleted(\Illuminate\Events\QueuedClosure|\Closure|string|array $callback)
 */
trait HasChromaCollection
{
    private static ?CollectionResource $chromaCollection;

    protected static function bootHasChromaCollection(): void
    {
        $model = new static();

        static::$chromaCollection = ChromaDB::getOrCreateCollection(
            name: $model->collectionName(),
            embeddingFunction: $model->embeddingFunction(),
        );

        if (config('chromadb.sync.enabled')) {
            static::saved(function (self $model) {
                if (!config('chromadb.sync.queue', false)) {
                    UpdateChromaCollectionJob::dispatchSync($model);
                } else {
                    UpdateChromaCollectionJob::dispatch($model);
                }
            });

            static::deleted(function (self $model) {
                self::getChromaCollection()->delete([$model->getKey()]);
            });
        }
    }

    public static function getChromaCollection(): CollectionResource
    {
        return static::$chromaCollection;
    }

    public function scopeQueryChromaCollection(Builder $query, string $queryText, int $nResults = 10): void
    {
        $queryResponse = self::getChromaCollection()->query(
            queryTexts: [$queryText],
            nResults: $nResults,
        );

        $ids = $queryResponse->ids[0];

        // Create a temporary table expression for sorting
        $tempTableExpression = collect($ids)->map(function ($id, $index) use ($queryResponse) {
            $distance = $queryResponse->distances[0][$index];
            return "SELECT $id AS id, $distance AS distance";
        })->implode(' UNION ALL ');

        // Join with the temporary table expression for sorting
        $query->join(DB::raw("($tempTableExpression) AS temp_chroma_sort"), function ($join) {
            $join->on("{$this->getTable()}.id", '=', 'temp_chroma_sort.id');
        });

        // Order the query based on the distances
        $query->orderBy('temp_chroma_sort.distance');
    }

    /**
     * The fields that should be used to create the Chroma metadata.
     *
     * @return string[]
     */
    protected function metadataFields(): array
    {
        return ['id'];
    }

    /**
     * The fields that should be used to create the Chroma document.
     *
     * @return string[]
     */
    protected function documentFields(): array
    {
        return [];
    }

    /**
     * The embedding function to use for the collection.
     */
    protected function embeddingFunction(): ?EmbeddingFunction
    {
        return null;
    }

    /**
     * The collection name to use for the model.
     */
    protected function collectionName(): string
    {
        return $this->getTable();
    }


    private function generateMetadata(): array
    {

        return collect($this->metadataFields())
            ->mapWithKeys(function (string $field) {
                return [$field => $this->getAttribute($field)];
            })
            ->toArray();
    }

    protected function generateChromaDocument(): string
    {
        return collect($this->documentFields())
            ->map(function (string $field) {
                return "$field : {$this->getAttribute($field)}";
            })
            ->join(' ; ', ' and ');
    }

    public static function truncateChromaCollection(): void
    {
        $model = new static();

        ChromaDB::deleteCollection(self::getChromaCollection()->name);

        static::$chromaCollection = ChromaDB::getOrCreateCollection(
            name: $model->collectionName(),
            embeddingFunction: $model->embeddingFunction(),
        );;
    }
}