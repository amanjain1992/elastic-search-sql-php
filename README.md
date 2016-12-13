elasticsearch-integrated -php

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "aman.j/elastic-search-sql-php": "dev-master"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

It helps to return elastic search result into array format using php
```php
include ('vendor/autoload.php');

use  Elasticsearch\Php;

$map = new \Elasticsearch\Php\ElasticSearchSqlConverter();
$jsonData = <Query output data from elastic search>;
$handler  = $map->create($jsonData, 1, 1, 1);
```