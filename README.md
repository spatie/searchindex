Store and retrieve objects from Elasticsearch
=================
[![Build status](https://img.shields.io/travis/spatie/searchindex.svg)](https://travis-ci.org/spatie/searchindex)
[![Latest Version](https://img.shields.io/github/release/spatie/searchindex.svg?style=flat-square)](https://github.com/freekmurze/searchindex/releases)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/47cca532-7a48-4f62-ac66-77f9a0ef122e.svg)](https://insight.sensiolabs.com/projects/47cca532-7a48-4f62-ac66-77f9a0ef122e)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/searchindex.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/searchindex)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/searchindex.svg?style=flat-square)](https://packagist.org/packages/spatie/searchindex)

This is an opinionated Laravel package to store and retrieve objects from [Elasticsearch](http://www.elasticsearch.org). It was tailormade for a project I was working on and only provides the functionality that I needed. If you need full control over elasticsearch via PHP, take a look at [the official low-level client](https://github.com/elasticsearch/elasticsearch-php).

That being said, if you want an easy and simple syntax to work with elasticsearch, this is the package for you.

## Laravel compatibility

 Laravel  | searchindex
:---------|:----------
 4.2.x    | 1.x
 5.x      | 2.x

## Installation
To be able to use this package you should have a server with Elasticsearch installed. If you want to install it on your local development machine you can use [these instructions](https://github.com/fideloper/Vaprobash/blob/master/scripts/elasticsearch.sh) from the excellent [Vaprobash repo](https://github.com/fideloper/Vaprobash).

This package itself can be installed through Composer.

```bash
composer require spatie/searchindex
```

You must install this service provider.

```php

// Laravel 5: config/app.php

'providers' => [
    ...
    'Spatie\SearchIndex\SearchIndexServiceProvider',
    ...
];
```

This package also comes with a facade, which provides an easy way to call the the class.


```php

// Laravel 5: config/app.php

'aliases' => array(
	...
	'SearchIndex' => 'Spatie\SearchIndex\SearchIndexFacade',
	...
)
```


You can publish the config file of the package using artisan.

```bash
php artisan vendor:publish --provider="Spatie\SearchIndex\SearchIndexServiceProvider"
```

The options in the config file are set with sane default values and they should be self-explanatory.


## Usage

###Prepare your object

Objects that you want to store in the index should implement the provided ```Spatie\SearchIndex\Searchable```- interface. 

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
            'brand' => $this->brand->name
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

The searchindex will use the returned searchableType and searchableId to identify an object in the index. 

###Add an object to the index
If you are using the facade it couldn't be simpler.
```php
//$product is an object that implements the Searchable interface

SearchIndex::upsertToIndex($product)
```

###Update an object in the index
You probably would have guessed it.

```php
//$product is an object that implements the Searchable interface

SearchIndex::upsertToIndex($product)
```
###Remove an object from the index
Yep. Easy.

```php
//$product is an object that implements the Searchable interface

SearchIndex::removeFromIndex($product)
```

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
```$query``` should be an array that adheres to the scheme provided by [the elasticsearch documentation](http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/current/_search_operations.html).

A query to perform a fuzzy like search that operates all fields of the index could look like this:
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
                                        'like_text' => $query,
                                        'fuzziness' => 0.5,
                                    ],
                            ],

                    ],
            ]
    ];
```
The search results that come back are simply elasticsearch response elements serialized into an array. You can see [an example of a response](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-request-body.html) in the official elasticsearch documentation.

##Tests
This package comes with a set of unit tests. Every time the package gets updated [Travis CI](https://travis-ci.org) will automatically run them.

You can aslo run them manually. You'll have first run ```composer install --dev``` to install phpspec. After that's out of the way you can run the tests with ```vendor/bin/phpspec run```.






