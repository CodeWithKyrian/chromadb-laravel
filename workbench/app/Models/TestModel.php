<?php

namespace Workbench\App\Models;

use Codewithkyrian\ChromaDB\Concerns\HasChromaCollection;
use Codewithkyrian\ChromaDB\Contracts\ChromaModel;
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
}
