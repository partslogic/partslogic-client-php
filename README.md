# PartsLogic APIs Client Library for PHP #

<dl>
  <dt>Reference Docs</dt><dd><a href="https://www.postman.com/partslogic/workspace/partslogic-search-api/overview">https://www.postman.com/partslogic/workspace/partslogic-search-api/overview</a></dd>
  <dt>License</dt><dd>Apache 2.0</dd>
</dl>

The PartsLogic API Client Library enables you to work with Parts Logic APIs.

## Requirements ##
* [PHP 7.4 or higher](https://www.php.net/)

## Installation ##

You can use **Composer** or simply **Download the Release**

### Composer

The preferred method is via [composer](https://getcomposer.org/). Follow the
[installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have
composer installed.

Once composer is installed, execute the following command in your project root to install this library:

```sh
composer require partslogice/apiclient
```

## Examples ##

### Basic Example ###

```php
// include your composer dependencies
require_once 'vendor/autoload.php';

$client = new PartsLogic\Client();

// Required configurations
$client->apiKey = 'your-key';

// Optional configuration
$client->endpoint = 'https://sandbox.sunhammer.io';
```

Alternatively you can pass a config to the constructor

```php
// include your composer dependencies
require_once 'vendor/autoload.php';

$client = new PartsLogic\Client(array(
  'apiKey' => 'your-key',
  'endpoint' => 'https://sandbox.sunhammer.io',
));
```