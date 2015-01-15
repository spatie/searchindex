Store and retrieve objects from Elasticsearch
=================
[![Build Status](https://secure.travis-ci.org/freekmurze/searchindex)](http://travis-ci.org/freekmurze/searchindex)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/47cca532-7a48-4f62-ac66-77f9a0ef122e/mini.png)](https://insight.sensiolabs.com/projects/47cca532-7a48-4f62-ac66-77f9a0ef122e)
[![Latest Stable Version](https://poser.pugx.org/spatie/searchindex/version.png)](https://packagist.org/packages/spatie/searchindex)
[![License](https://poser.pugx.org/spatie/searchindex/license.png)](https://packagist.org/packages/spatie/searchindex)

This is an opinionated Laravel 4 | 5 package to store and retrieve objects from Elasticsearch.



## Installation
To be able to use this package you should have a server with Elasticsearch installed. If you want to install it on your local development machine you can use [these instructions](https://github.com/fideloper/Vaprobash/blob/master/scripts/elasticsearch.sh) from the excellent [Vaprobash repo](https://github.com/fideloper/Vaprobash).

This package itself can be installed through Composer.

```bash
composer require spatie/searchindex
```

You must install this service provider.

```php

// Laravel 4: app/config/app.php

'providers' => [
    '...',
    'Spatie\SearchIndex\SearchIndexServiceProvider'
];
```

This package also comes with a facade, which provides an easy way to call the the class.


```php

// Laravel 4: app/config/app.php

'aliases' => array(
	...
	'AnalyticsReports' => 'Spatie\SearchIndex\SearchIndexFacade',
)
```


You can publish the config file of the package using artisan

```bash
php artisan config:publish spatie/searchindex
```


## Usage


Coming soon    
