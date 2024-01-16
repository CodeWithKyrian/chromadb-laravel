<?php

declare(strict_types=1);

namespace Codewithkyrian\ChromaDB\Concerns;

use Codewithkyrian\ChromaDB\Contracts\ChromaModel;
use Codewithkyrian\ChromaDB\Embeddings\EmbeddingFunction;
use Codewithkyrian\ChromaDB\Facades\ChromaDB;
use Codewithkyrian\ChromaDB\Jobs\DeleteChromaCollectionItemJob;
use Codewithkyrian\ChromaDB\Jobs\UpdateChromaCollectionJob;
use Codewithkyrian\ChromaDB\Resources\CollectionResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

trait HasChromaCollection
{
    private static ?CollectionResource $chromaCollection;

    protected static function bootHasChromaCollection(): void
    {
        if (config('chromadb.sync.enabled')) {

            static::saved(function (Model&ChromaModel $model) {
                $changes = $model->wasRecentlyCreated ? $model->getAttributes() : $model->getChanges();
                $changedFields = array_keys($changes);

                if (!config('chromadb.sync.queue', false)) {
                    UpdateChromaCollectionJob::dispatchSync($model, $changedFields);
                } else {
                    UpdateChromaCollectionJob::dispatch($model, $changedFields);
                }
            });

            static::deleted(function (Model&ChromaModel $model) {
                if(!config('chromadb.sync.queue', false)) {
                    DeleteChromaCollectionItemJob::dispatchSync($model::class, $model->getKey());
                } else {
                    DeleteChromaCollectionItemJob::dispatch($model::class, $model->getKey());
                }
            });
        }
    }

    public static function getChromaCollection(): CollectionResource
    {
        $model = new static();

        static::$chromaCollection = ChromaDB::getOrCreateCollection(
            name: $model->collectionName(),
            embeddingFunction: $model->embeddingFunction(),
        );


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
    public function metadataFields(): array
    {
        return ['id'];
    }

    /**
     * The fields that should be used to create the Chroma document.
     *
     * @return string[]
     */
    public function documentFields(): array
    {
        return [];
    }

    /**
     * The embedding function to use for the collection.
     */
    public function embeddingFunction(): ?EmbeddingFunction
    {
        return null;
    }

    /**
     * The collection name to use for the model.
     */
    public function collectionName(): string
    {
        return $this->getTable();
    }


    public function generateMetadata(): array
    {

        return collect($this->metadataFields())
            ->mapWithKeys(function (string $field) {
                return [$field => $this->getAttribute($field)];
            })
            ->toArray();
    }

    public function generateChromaDocument(): string
    {
        return collect($this->documentFields())
            ->map(function (string $field) {
                return "$field : {$this->getAttribute($field)}";
            })
            ->join(' ; ', ' and ');
    }

    public static function truncateChromaCollection(): void
    {
        $collection = self::getChromaCollection();

        $collection->delete($collection->get()->ids);
    }
}
