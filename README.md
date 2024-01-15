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
];
```

As you can see, all configuration options are retrieved from environment variables, so you can easily set them in
your `.env` file without having to modify the configuration file itself.

```dotenv
CHROMA_HOST=http://localhost
CHROMA_PORT=8080
CHROMA_TENANT=default
CHROMA_DATABASE=default
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








