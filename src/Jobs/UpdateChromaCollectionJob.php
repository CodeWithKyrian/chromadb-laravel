<?php

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
        public Model&ChromaModel $model,
    )
    {
        $this->onQueue(config('chromadb.sync.queue'));
        $this->onConnection(config('chromadb.sync.connection'));
    }

    public function handle(): void
    {
       $this->model::getChromaCollection()->upsert(
            ids: [strval($this->model->getKey())],
            metadatas: [$this->model->generateMetadata()],
            documents: [$this->model->generateChromaDocument()],
        );
    }

    public function tries(): int
    {
        return config('chromadb.sync.tries');
    }
}
