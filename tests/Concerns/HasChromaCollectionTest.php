<?php

declare(strict_types=1);

use Codewithkyrian\ChromaDB\Concerns\HasChromaCollection;
use Codewithkyrian\ChromaDB\Jobs\UpdateChromaCollectionJob;
use Codewithkyrian\ChromaDB\Resources\CollectionResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Workbench\App\Models\TestModel;

it('creates a chroma collection on boot', function () {

    $collection = TestModel::getChromaCollection();
    expect($collection)
        ->toBeInstanceOf(CollectionResource::class)
        ->and($collection->name)->toBe('test_models');
});

it('dispatches a job to update the collection on save', function () {
    Queue::fake();

    $model = TestModel::create([
        'title' => 'Test Model',
        'document' => 'This is a test model'
    ]);

    Queue::assertPushed(UpdateChromaCollectionJob::class);
});