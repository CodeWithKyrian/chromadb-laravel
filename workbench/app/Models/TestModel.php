<?php

namespace Workbench\App\Models;

use Codewithkyrian\ChromaDB\Concerns\HasChromaCollection;
use Codewithkyrian\ChromaDB\Contracts\ChromaModel;
use Codewithkyrian\ChromaDB\Embeddings\EmbeddingFunction;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model implements ChromaModel
{
    use HasChromaCollection;

    protected $fillable = [
        'title',
        'document',
    ];

    public function documentFields(): array
    {
        return [
            'document',
        ];
    }

    public function embeddingFunction(): ?EmbeddingFunction
    {
        return new class implements EmbeddingFunction {
            public function generate(array $texts): array
            {
                return array_map(function ($text) {
                    return [1.0, 2.0, 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 9.0, 10.0];
                }, $texts);
            }
        };
    }
}
