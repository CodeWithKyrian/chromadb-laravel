<?php

declare(strict_types=1);

use Codewithkyrian\ChromaDB\Concerns\HasChromaCollection;
use Codewithkyrian\ChromaDB\Jobs\DeleteChromaCollectionItemJob;
use Codewithkyrian\ChromaDB\Jobs\UpdateChromaCollectionJob;
use Codewithkyrian\ChromaDB\Resources\CollectionResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Workbench\App\Models\TestModel;

it('creates a chroma collection on boot', function () {

    $collection = TestModel::getChromaCollection();
    expect($collection)
        ->toBeInstanceOf(CollectionResource::class)
        ->and($collection->name)->toBe('test_models');
});

it('dispatches an async job to update the collection on save', function () {
    Queue::fake();

    TestModel::create([
        'title' => 'Test Model',
        'document' => 'This is a test model'
    ]);

    Queue::assertPushedOn(config('chromadb.sync.queue'), UpdateChromaCollectionJob::class);
});

it('dispatches an async job to update the collection on delete', function () {
    Queue::fake();

    $model = TestModel::create([
        'title' => 'Test Model',
        'document' => 'This is a test model'
    ]);

    $model->delete();

    Queue::assertPushedOn(config('chromadb.sync.queue'), DeleteChromaCollectionItemJob::class);
});

it('dispatches a sync job to update the collection on save', function () {
    config(['chromadb.sync.queue' => false]);

    $model = TestModel::create([
        'title' => 'Test Model',
        'document' => 'This is a test model'
    ]);

    $collection = TestModel::getChromaCollection();

    expect($collection->count())->toBe(1);
});

it('dispatches a sync job to update the collection on delete', function () {
    config(['chromadb.sync.queue' => false]);

    $model = TestModel::create([
        'title' => 'Test Model',
        'document' => 'This is a test model'
    ]);

    $collection = TestModel::getChromaCollection();

    expect($collection->count())->toBe(1);

    $model->delete();

    expect($collection->count())->toBe(0);
});

it('does not dispatch a job to update the collection on save when sync is disabled', function () {
    config(['chromadb.sync.enabled' => false]);

    Queue::fake();

    TestModel::create([
        'title' => 'Test Model',
        'document' => 'This is a test model'
    ]);

    Queue::assertNotPushed(UpdateChromaCollectionJob::class);
});

it('does not dispatch a job to update the collection on delete when sync is disabled', function () {
    config(['chromadb.sync.enabled' => false]);

    Queue::fake();

    $model = TestModel::create([
        'title' => 'Test Model',
        'document' => 'This is a test model'
    ]);

    $model->delete();

    Queue::assertNotPushed(DeleteChromaCollectionItemJob::class);
});

it('does not dispatch a job to update the collection on save when queue is disabled', function () {
    config(['chromadb.sync.queue' => false]);

    Bus::fake();

    TestModel::create([
        'title' => 'Test Model',
        'document' => 'This is a test model'
    ]);

    Bus::assertDispatchedSync(UpdateChromaCollectionJob::class);
});

it('does not dispatch a job to update the collection on delete when queue is disabled', function () {
    config(['chromadb.sync.queue' => false]);

    Bus::fake();

    $model = TestModel::create([
        'title' => 'Test Model',
        'document' => 'This is a test model'
    ]);

    $model->delete();

    Bus::assertDispatchedSync(DeleteChromaCollectionItemJob::class);
});

it('can truncate the collection', function () {
    config(['chromadb.sync.queue' => false]);

    $collection = TestModel::getChromaCollection();

    expect($collection->count())->toBe(0);

    TestModel::create([
        'title' => 'Test Model',
        'document' => 'This is a test model'
    ]);

    expect($collection->count())->toBe(1);

    TestModel::truncateChromaCollection();

    expect($collection->count())->toBe(0);
});

it('applies the correct scope to the query', function () {
    $query = TestModel::queryChromaCollection('test');

    expect($query->toSql())
        ->toContain('test_models', 'temp_chroma_sort');
});

