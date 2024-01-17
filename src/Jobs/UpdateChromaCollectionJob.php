<?php

declare(strict_types=1);

namespace Codewithkyrian\ChromaDB\Jobs;

use Codewithkyrian\ChromaDB\Contracts\ChromaModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateChromaCollectionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Model&ChromaModel $model,
        protected array             $changedFields,
    )
    {
        $this->onQueue(config('chromadb.sync.queue'));
        $this->onConnection(config('chromadb.sync.connection'));
    }

    public function handle(): void
    {
        // If there are no changes, no need to update the chroma collection
        if (empty($this->changedFields)) return;

        $metadata = null;
        $document = null;

        // Helper function to check if any fields in fieldsArray are among the changed attributes
        $fieldsChanged = function ($fieldsArray){
            return $fieldsArray && count(array_intersect($fieldsArray, $this->changedFields)) > 0;
        };

        if ($fieldsChanged($this->model->metadataFields())) {
            $metadata = $this->model->toChromaMetadata();
        }

        if ($fieldsChanged($this->model->documentFields())) {
            $document = $this->model->toChromaDocument();
        }

        // If any of the fields in metadataFields() or documentFields() is among the changed attributes
        if ($metadata || $document) {
            $this->model::getChromaCollection()->upsert(
                ids: [strval($this->model->getKey())],
                metadatas: $metadata ? [$metadata] : null,
                documents: $document ? [$document] : null,
            );
        }
    }

    public function tries(): int
    {
        return config('chromadb.sync.tries');
    }
}
