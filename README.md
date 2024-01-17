## ChromaDB PHP for Laravel

**A Laravel convenient wrapper for the ChromaDB PHP library, used to interact
with [Chroma](https://github.com/chroma-core/chroma) vector database seamlessly.**

[![MIT Licensed](https://img.shields.io/badge/license-mit-blue.svg)](https://github.com/CodeWithKyrian/chromadb-laravel/blob/main/LICENSE)
[![GitHub Tests Action Status](https://github.com/CodeWithKyrian/chromadb-laravel/actions/workflows/tests.yml/badge.svg)](https://github.com/CodeWithKyrian/chromadb-laravel/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/codewithkyrian/chromadb-laravel.svg)](https://packagist.org/packages/codewithkyrian/chromadb-laravel)


> **Note:** This package is a wrapper around the [ChromaDB PHP library](https://github.com/CodeWithKyrian/chromadb-php).
> It is meant to be used in Laravel applications.
> If you are looking for a standalone or framework-agnostic way of interacting with Chroma in PHP, check out
> the [ChromaDB PHP library](https://github.com/CodeWithKyrian/chromadb-php) instead.

## Installation

You can install the package via composer:

```bash
composer require codewithkyrian/chromadb-laravel
```

After installing the package, you can publish the configuration file using the following command:

```bash
php artisan vendor:publish --provider="Codewithkyrian\ChromaDB\ChromaServiceProvider" --tag="config"
```

This will publish a `chromadb.php` file in your config directory with the following content:

```php
return [
    /*
     |--------------------------------------------------------------------------
     | ChromaDB Host
     |--------------------------------------------------------------------------
     |
     | Here you may specify your ChromaDB Host. This is the host where your ChromaDB
     | instance is running. This is used to connect to your ChromaDB instance.
     */
    'host' => env('CHROMA_HOST', 'localhost'),

    /*
     |--------------------------------------------------------------------------
     | ChromaDB Port
     |--------------------------------------------------------------------------
     |
     | Here you may specify your ChromaDB Port. This is the port where your ChromaDB
     | instance is running. This is used to connect to your ChromaDB instance.
     */
    'port' => env('CHROMA_PORT', 8000),

    /*
     |--------------------------------------------------------------------------
     | ChromaDB Tenant
     |--------------------------------------------------------------------------
     |
     | This is the tenant that you want to connect to.
     */
    'tenant' => env('CHROMA_TENANT', 'default_tenant'),

    /*
     |--------------------------------------------------------------------------
     | ChromaDB Database
     |--------------------------------------------------------------------------
     |
     | This is the database that you want to connect to.
     */
    'database' => env('CHROMA_DATABASE', 'default_database'),


    /*
     |--------------------------------------------------------------------------
     | ChromaDB Sync
     |--------------------------------------------------------------------------
     |
     | This is the configuration for the ChromaDB Sync feature. This feature
     | allows you to sync data from your local database to your ChromaDB
     | instance.
     */
    'sync' => [

        /*
         |--------------------------------------------------------------------------
         | ChromaDB Sync Enabled
         |--------------------------------------------------------------------------
         |
         | This option controls whether the ChromaDB Sync feature is enabled. If
         | this is set to false, then the ChromaDB Sync feature will not be
         | enabled.
         */
        'enabled' => env('CHROMA_SYNC_ENABLED', true),

        /*
         |--------------------------------------------------------------------------
         | ChromaDB Sync Queue
         |--------------------------------------------------------------------------
         |
         | This option controls which queue the ChromaDB Sync feature will use.
         | This is used to queue the sync jobs. Set to false to disable queueing
         | and run the sync jobs immediately.
         */
        'queue' => env('CHROMA_SYNC_QUEUE', 'default'),

        /*
         |--------------------------------------------------------------------------
         | ChromaDB Sync Connection
         |--------------------------------------------------------------------------
         |
         | This option controls which connection the ChromaDB Sync feature will use.
         | This is used to queue the sync jobs.
         */
        'connection' => env('CHROMA_SYNC_CONNECTION', 'database'),

        /*
         |--------------------------------------------------------------------------
         | ChromaDB Sync Tries
         |--------------------------------------------------------------------------
         |
         | This option controls how many times the Job will be retried if it fails
         | while trying to sync the data to ChromaDB.
         */
        'tries' => env('CHROMA_SYNC_TRIES', 3),

    ],
];
```

As you can see, all configuration options are retrieved from environment variables, so you can easily set them in
your `.env` file without having to modify the configuration file itself.

```dotenv
CHROMA_HOST=http://localhost
CHROMA_PORT=8080
CHROMA_TENANT=default
CHROMA_DATABASE=default
CHROMA_SYNC_ENABLED=true
CHROMA_SYNC_QUEUE=default
CHROMA_SYNC_CONNECTION=database
CHROMA_SYNC_TRIES=3
```

## Usage

Of course, you need to have the ChromaDB server running before you can use this package. Instructions on how to run
ChromaDB can be found in the [ChromaDB website](https://docs.trychroma.com/deployment).

```php
use Codewithkyrian\ChromaDB\Facades\ChromaDB;

ChromaDB::version(); // Eg. 0.4.2

$collection = ChromaDB::createCollection('collection_name');

$collection = ChromaDB::getCollection('collection_name');

$collection = ChromaDB::deleteCollection('collection_name');

$collections = ChromaDB::listCollections();
```

For more usage examples, check out the [ChromaDB PHP library](https://github.com/CodeWithKyrian/chromadb-php).

## Working with Eloquent Models

This package comes with a trait that you can use to associate your Eloquent models with a ChromaDB collection and
automatically sync them to ChromaDB. To get started, add the ChromaModel interface and HasChromaCollection trait to your
model.

```php
use Codewithkyrian\ChromaDB\Contracts\ChromaModel;
use Codewithkyrian\ChromaDB\Concerns\HasChromaCollection;

class User extends Model implements ChromaModel
{
    use HasChromaCollection;
    
    // ...
}
```

After that, there are a few methods that you need to implement in your model.

- `documentFields()` - This method should return an array of fields that you want to use to form the document that will
  be embedded in the ChromaDB collection. If combines the fields in this array to a string and uses that as the
  document. This method is optional, but if you don't implement it, you must implement the `toChromaDocument()` method.
  ```php
    public function documentFields(): array
    {
        return [
            'first_name',
            'last_name',
        ];
    }
    ```

- `embeddingFunction()` - This method should return the name of the embedding function that you want to use to embed
  your model in the ChromaDB collection. You can use any of
  the [built-in embedding functions](https://github.com/CodeWithKyrian/chromadb-php?tab=readme-ov-file#passing-in-embedding-function)
  or create your own embedding function by implementing the EmbeddingFunction interface (including Anonymous Classes).
    ```php
        use Codewithkyrian\ChromaDB\Embeddings\JinaEmbeddingFunction;
  
        public function embeddingFunction(): string
        {
            return new JinaEmbeddingFunction('jina-api-key');
        }
    ```
- `collectionName()` - This method should return the name you want for the ChromaDB collection associated with your
  model. By default, it returns the model's table name.
- `toChromaDocument()` (optional) - If you don't like the default way of combining the fields in the `documentFields()`
  method, you can implement this method to return the document that will be embedded in the ChromaDB collection.
  ```php
    public function toChromaDocument(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    ```
- `metadataFields()` (optional) - This method should return an array of fields that you want to use to form the metadata
  that will be embedded in the ChromaDB collection. It'll be saved as a json object in the ChromaDB collection. By
  default, it only returns the `id` field, so the metadata will be `{ "id": 1 }`.
    ```php
        public function metadataFields(): array
        {
            return [
                'id',
                'first_name',
                'last_name',
            ];
        }
    ```
- `toChromaMetadata()` (optional) - If you want more control over the metadata that will be embedded in the ChromaDB
  collection, you can implement this method to return the metadata that will be embedded in the ChromaDB collection. Be
  sure to return an associative array.
  ```php
    public function toChromaMetadata(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
        ];
    }
    ```

After implementing the methods above (only two are required), you model now has a `getChromaCollection()` method that
you can use to get the ChromaDB collection associated with your model.

```php
$collection = User::getChromaCollection();

$collection->name; // users
$collection->count();
```

### Syncing Models to ChromaDB

By default, the package will automatically sync your models to ChromaDB whenever they are created, updated or deleted
provided there was a change in the attributes since the last sync.
You can disable this by setting the `chromadb.sync.enabled` config option to `false` or better still, set
the `CHROMA_SYNC_ENABLED`
to false.

The syncing of models is queued so be sure to set up your queue and workers the Laravel recommended way. You can set the
queue, connection
and the number tries for the job in the config or using the  `CHROMA_SYNC_QUEUE`, `CHROMA_SYNC_CONNECTION`
and `CHROMA_SYNC_TRIES` respectively.
However, you can set the `CHROMA_SYNC_QUEUE` to false to disable using queues to perform the sync.

### Querying the collection

While you can still query the collection after getting it from the `getChromaCollection()` method, you can also query
the collection
using the model. The model has a `queryChromaCollection()` scope that you can use to query the collection.

```php
$searchTerm = 'Kyrian';

$users = User::queryChromaCollection($searchTerm, 10)
            ->where('first_name', 'John')
            ->get();
```

The arguments for the `queryChromaCollection()` method are the same as the `query()` method in the ChromaDB PHP library.
Also, this meethod
sorts the results by the `distance` field in the results.

### Truncating the collection

You can truncate the collection associated with a model using the `truncateChromaCollection()` method on the model.

```php

User::truncateChromaCollection();
```

## Testing

```
// Run chroma by running the docker compose file in the repo
docker compose up -d

composer test
```

## Contributors

- [Kyrian Obikwelu](https://github.com/CodeWithKyrian)
- Other contributors are welcome.

## License

This project is licensed under the MIT License. See
the [LICENSE](https://github.com/codewithkyrian/chromadb-php/blob/main/LICENSE) file for more information.








