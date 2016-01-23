# LogStream client

This is a PHP library that is a client for LogStream.

## Getting started

This library can be used both as standalone or with the Symfony integration.

### Standalone

```php
use GuzzleHttp\Client;
use LogStream\Client\Http\JsonSerializableNormalizer;
use LogStream\Client\HttpClient;
use LogStream\TreeLoggerFactory;

$loggerFactory = new TreeLoggerFactory(
    new HttpClient(
        new Client(),
        new JsonSerializableNormalizer(),
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
    url: https://api.logstream.io
```

#### Configuration reference

```yml
log_stream:
    # Address of LogStream API
    url: https://api.logstream.io
```

## Operation runner

An interesting feature is the integration with the [FaultTolerance library](https://github.com/sroze/Tolerance):
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
