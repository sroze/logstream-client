<?php

namespace LogStream\Client;

use FaultTolerance\Operation\Callback;
use FaultTolerance\OperationBuffer\InMemoryOperationBuffer;
use FaultTolerance\OperationRunner;
use FaultTolerance\OperationRunner\BufferedOperationRunner;
use FaultTolerance\OperationRunner\RetryOperationRunner;
use FaultTolerance\OperationRunner\SimpleOperationRunner;
use FaultTolerance\Waiter;
use FaultTolerance\Waiter\SleepWaiter;
use FaultTolerance\WaitStrategy\Exponential;
use FaultTolerance\WaitStrategy\Max;
use LogStream\Client;
use LogStream\Log;
use LogStream\LogNode;

class RetryBufferDecorator implements Client
{
    /**
     * @var OperationRunner
     */
    private $operationRunner;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     * @param Waiter $waiter
     */
    public function __construct(Client $client, Waiter $waiter)
    {
        $operationRunner = new SimpleOperationRunner();
        $operationRunner = new RetryOperationRunner(
            $operationRunner,
            new Max(
                new Exponential(
                    $waiter,
                    1
                ),
                10
            )
        );

        $this->operationRunner = new BufferedOperationRunner($operationRunner, new InMemoryOperationBuffer());
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function create(LogNode $log, Log $parent = null)
    {
        $operation = new Callback(function() use ($log, $parent) {
            return $this->client->create($log, $parent);
        });

        $this->operationRunner->run($operation);

        return $operation->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus(Log $log, $status)
    {
        $operation = new Callback(function() use ($log, $status) {
            return $this->client->updateStatus($log, $status);
        });

        $this->operationRunner->run($operation);

        return $operation->getResult();
    }
}
