<?php

declare(strict_types=1);

use Codewithkyrian\ChromaDB\Concerns\HasChromaCollection;
use Codewithkyrian\ChromaDB\Resources\CollectionResource;
use Illuminate\Database\Eloquent\Model;

it('creates a chroma collection on boot', function () {
    $model = new class extends Model {
        use HasChromaCollection;

        public function collectionName(): string
        {
            return 'test_collection';
        }
    };

    $collection = $model->getChromaCollection();
    expect($collection)
        ->toBeInstanceOf(CollectionResource::class)
        ->and($collection->name)->toBe('test_collection');
});