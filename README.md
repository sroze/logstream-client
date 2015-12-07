# LogStream client

This is a PHP library that is a client for LogStream.

## Getting started

This library can be used both as standalone or with the Symfony integration.

### Standalone

```php
use GuzzleHttp\Client;
use LogStream\Client\Http\LogNormalizer;
use LogStream\Client\HttpClient;
use LogStream\TreeLoggerFactory;

$loggerFactory = new TreeLoggerFactory(
    new HttpClient(
        new Client(),
        new LogNormalizer(),
        $address
    )
);
```

### Symfony integration

The library contains a Symfony bundle. In order to activate it, you simply have to add in in your `AppKernel.php` file:

```php
$bundles = [
    // ...
    new LogStream\LogStreamBundle(),
];
```

Then, simply adds the configuration:
```yml
log_stream:
    url: ws://address-of-logstream:8080
    websocket: true

    # You can also uses the _slow_ HTTP API by using an HTTP `url` and then setting `websocket` to false
    # or simply removing the `websocket` configuration.
```
