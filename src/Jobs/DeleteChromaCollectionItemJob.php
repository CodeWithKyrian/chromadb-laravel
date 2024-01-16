<?php

namespace Codewithkyrian\ChromaDB\Jobs;

use Codewithkyrian\ChromaDB\Contracts\ChromaModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteChromaCollectionItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param class-string<ChromaModel> $model
     * @param string $key
     */
    public function __construct(protected string $model, protected string $key)
    {
        $this->onQueue(config('chromadb.sync.queue'));
        $this->onConnection(config('chromadb.sync.connection'));
    }

    public function handle(): void
    {
        $this->model::getChromaCollection()->delete([$this->key]);
    }
}
