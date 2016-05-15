Store and retrieve objects from a search index
=================
[![Latest Version](https://img.shields.io/github/release/spatie/searchindex.svg?style=flat-square)](https://github.com/spatie/searchindex/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build status](https://img.shields.io/travis/spatie/searchindex.svg?style=flat-square)](https://travis-ci.org/spatie/searchindex)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/47cca532-7a48-4f62-ac66-77f9a0ef122e.svg)](https://insight.sensiolabs.com/projects/47cca532-7a48-4f62-ac66-77f9a0ef122e)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/searchindex.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/searchindex)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/searchindex.svg?style=flat-square)](https://packagist.org/packages/spatie/searchindex)

This is an opinionated Laravel 5.1 package to store and retrieve objects from a search index.
Currently [Elasticsearch](http://www.elasticsearch.org) and [Algolia](https://www.algolia.com) are supported.

Once the package is installed objects can be easily indexed and retrieved:
```php
//$product is an object that implements the Searchable interface
SearchIndex::upsertToIndex($product);

SearchIndex::getResults('look for this');
```

Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our
open source projects [on our website](https://spatie.be/opensource).

## Installation
This package can be installed through Composer.

```bash
composer require spatie/searchindex
```

You must install this service provider.

```php
// config/app.php
'providers' => [
    ...
    Spatie\SearchIndex\SearchIndexServiceProvider::class,
];
```

This package also comes with a facade, which provides an easy way to call the the class.


```php
// config/app.php
'aliases' => [
	...
	'SearchIndex' => Spatie\SearchIndex\SearchIndexFacade::class,
]
```

You can publish the config-file with:
```bash
php artisan vendor:publish --provider="Spatie\SearchIndex\SearchIndexServiceProvider"
```

The options in the config file are set with sane default values and they should
be self-explanatory. 

The next installation steps depend on if you want to use Elasticsearch or Algolia.

###Elasticsearch
To use Elasticsearch you must install the official 1.x series low level client:
```bash
composer require elasticsearch/elasticsearch "^1.3"
```

You also should have a server with Elasticsearch installed.
If you want to install it on your local development machine you can
use [these instructions](https://github.com/fideloper/Vaprobash/blob/master/scripts/elasticsearch.sh)
from the excellent [Vaprobash repo](https://github.com/fideloper/Vaprobash).


###Algolia
To use Algolia you must install the official low level client:
```bash
composer require algolia/algoliasearch-client-php
```

## Usage

###Prepare your object

Objects that you want to store in the index should implement the
provided ```Spatie\SearchIndex\Searchable```- interface.

```php
namespace Spatie\SearchIndex;

interface Searchable {

    /**
     * Returns an array with properties which must be indexed
     *
     * @return array
     */
    public function getSearchableBody();

    /**
     * Return the type of the searchable subject
     *
     * @return string
     */
    public function getSearchableType();

    /**
     * Return the id of the searchable subject
     *
     * @return string
     */
    public function getSearchableId();
```

Here is an example how you could implement it with an Eloquent model:

```php
class Product extends Eloquent implements Searchable
{
    
    ...
    
    /**
     * Returns an array with properties which must be indexed
     *
     * @return array
     */
    public function getSearchableBody()
    {
        $searchableProperties = [
            'name' => $this->name,
            'brand' => $this->brand->name,
            'category' => $this->category->name
        ];
        
        return $searchableProperties;

    }

    /**
     * Return the type of the searchable subject
     *
     * @return string
     */
    public function getSearchableType()
    {
        return 'product';
    }

    /**
     * Return the id of the searchable subject
     *
     * @return string
     */
    public function getSearchableId()
    {
        return $this->id;
    }
}
```

The searchindex will use the returned searchableType and searchableId to
identify an object in the index.

###Add an object to the index
If you are using the facade it couldn't be simpler.
```php
//$product is an object that implements the Searchable interface

SearchIndex::upsertToIndex($product);
```

###Update an object in the index
You probably would have guessed it.

```php
//$product is an object that implements the Searchable interface

SearchIndex::upsertToIndex($product);
```
###Remove an object from the index
Yep. Easy.

```php
//$product is an object that implements the Searchable interface

SearchIndex::removeFromIndex($product);
```

Alternatively you can remove an object from the index by passing type and id:

```php
SearchIndex::removeFromIndexByTypeAndId('product', 1);
```
This can be handy when you've already deleted your model.

###Clear the entire index
If only you could to this with your facebook account.

```php
SearchIndex::clearIndex();
```

###Perform a search on the index
You can retrieve search results with this method:
```php
SearchIndex::getResults($query);
```

####Elasticsearch
```$query``` should be an array that adheres to the scheme provided
by [the elasticsearch documentation](http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/current/_search_operations.html).

A query to perform a fuzzy like search that operates all fields of the index could
look like this:
```php
$query =
    [
        'body' =>
            [
                'from' => 0,
                'size' => 500,
                'query' =>
                    [
                        'fuzzy_like_this' =>
                            [
                                '_all' =>
                                    [
                                        'like_text' => 'look for this',
                                        'fuzziness' => 0.5,
                                    ],
                            ],
                    ],
            ]
    ];
```
The search results that come back are simply elasticsearch response elements
serialized into an array. You can see [an example of a response](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-request-body.html)
in the official elasticsearch documentation.

####Algolia
You can just pass a string to search the index:
```php
SearchIndex::getResults('look for this');
```
To perform more advanced queries an array may be passed. Read
the [official documentation](https://github.com/algolia/algoliasearch-client-php#search) to learn what's possible.



###All other operations
For all other operations you can get the underlying client:
```php
SearchIndex::getClient(); // will return the Elasticsearch or Algolia client.
```

##Query helpers

If you're using Algolia you can use a `SearchQuery`-object to perform searches.

```php
use Spatie\SearchIndex\Query\Algolia\SearchIndex();

$searchQuery = new SearchQuery();
$searchQuery->searchFor('my query')
            ->withFacet('facetName', 'facetValue');

//a searchQuery object may be passed to the getResults-function directly.
SearchIndex::getResults($searchQuery);
```

##Tests
This package comes with a set of unit tests. Every time the package
gets updated [Travis CI](https://travis-ci.org) will automatically run them.

You can also run them manually. You'll have first run ```composer install --dev``` to install phpspec. After that's out of the way you can run the tests with ```vendor/bin/phpspec run```.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [freek@spatie.be](mailto:freek@spatie.be) instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

##About Spatie
Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all
our open source projects [on our website](https://spatie.be/opensource).
