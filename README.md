Store and retrieve objects from Elasticsearch
=================
[![Latest Stable Version](https://poser.pugx.org/spatie/searchindex/version.png)](https://packagist.org/packages/spatie/searchindex)
[![License](https://poser.pugx.org/spatie/searchindex/license.png)](https://packagist.org/packages/spatie/searchindex)

This is an opinionated Laravel 4 | 5 package to retrieve Google Analytics data.



## Installation

This package can be installed through Composer.

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
