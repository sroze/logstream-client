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

#### Configuration reference

```yml
log_stream:
    # Address of the LogStream WebSocket or API
    url: ws://logstream:8080

    # (optional, default false) Uses websocket or not?
    websocket: true

    # (optional, default null) Use the following operation runner (service ID) to decorates the client
    operation_runner: my.operation_runner
```

## Operation runner

An interesting feature is the integration with the [FaultTolerance library](https://github.com/sroze/FaultTolerance):
there's a client decorator, `OperationRunnerDecorator` that accepts an operation runner to run the client's calls. That
way you can easily have a retry feature in case of problems in the real-time stream:

```php
use LogStream\Client\FaultTolerance\OperationRunnerDecorator;

use FaultTolerance\OperationRunner\SimpleOperationRunner;
use FaultTolerance\OperationRunner\RetryOperationRunner;
use FaultTolerance\Waiter\SleepWaiter;
use FaultTolerance\WaitStrategy\Exponential;

$runner = new RetryOperationRunner(
    new SimpleOperationRunner(),
    new Max(new Exponential(new SleepWaiter(), 0.1), 10)
);

$client = new OperationRunnerDecorator($client, $operationRunner);
```
