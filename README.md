# Zoom

Zoom API for Codeigniter 4

## Installation via composer

Use the package with composer install

	> composer require daycry/zoom

## Manual installation

Download this repo and then enable it by editing **app/Config/Autoload.php** and adding the **Daycry\Zoom**
namespace to the **$psr4** array. For example, if you copied it into **app/ThirdParty**:

```php
$psr4 = [
    'Config'      => APPPATH . 'Config',
    APP_NAMESPACE => APPPATH,
    'App'         => APPPATH,
    'Daycry\Zoom' => APPPATH .'ThirdParty/zoom/src',
];
```

## Configuration

Run command:

	> php spark zoom:publish

This command will copy a config file to your app namespace.


## Usage Loading Library

```php
$zoom = new \Daycry\Zoom\Zoom();

```

## Usage as a Service

```php
$zoom = \Config\Services::zoom();

```

## Usage as a Helper

In your BaseController - $helpers array, add an element with your helper filename.

```php
protected $helpers = [ 'zoom_helper' ];

```

And then, you can use the helper

```php

$zoom = zoom_instance();


```

## Authentication

```php
/**
 *
 * @return AccessTokenInterface
 */

$zoom = new \Daycry\Zoom\Zoom();
$token = $zoom->authentication();

echo "<pre>";
echo json_encode( $token );
echo "</pre>";

```

## Request

```php
/**
 * Returns an authenticated PSR-7 request instance.
 *
 * @param  string $method
 * @param  string $url
 * @return RequestInterface
 */

$zoom = new \Daycry\Zoom\Zoom();
$zoom->setAccessToken( $token );
$reponse = $zoom->request( 'GET', 'users' );

echo "<pre>";
var_dump( $reponse );
echo "</pre>";

```

You can pass extra parametres into the request method.


```php
/**
 * Returns an authenticated PSR-7 request instance.
 *
 * @param  string $method
 * @param  string $url
 * @param  array $options Any of "headers", "body", and "protocolVersion".
 * @param  AccessTokenInterface|string $token
 * @return RequestInterface
 */

$zoom = new \Daycry\Zoom\Zoom();
$zoom->setAccessToken( $token );
$reponse = $zoom->request( 'GET', 'users', [], $token );

echo "<pre>";
var_dump( $reponse );
echo "</pre>";

```

## Refresh Token

```php
/**
 *
 * @return AccessTokenInterface
 */

$zoom = new \Daycry\Zoom\Zoom();
$zoom->setAccessToken( $token );

$reponse = $zoom->refreshAccessToken();

echo "<pre>";
var_dump( $reponse );
echo "</pre>";

```

## Example Token to save in your database

```php

{"token_type":"bearer","scope":"dashboard_crc:read:admin","access_token":"xxxxx","refresh_token":"xxxxxx","expires":1586716974}

```

## Sample Code

[Example](https://github.com/daycry/example-zoom).
