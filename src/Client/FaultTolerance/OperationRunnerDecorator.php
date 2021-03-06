<?php

namespace LogStream\Client\FaultTolerance;

use FaultTolerance\Operation\Callback;
use FaultTolerance\OperationRunner;
use LogStream\Client;
use LogStream\Client\ClientException;
use LogStream\Log;
use LogStream\LogNode;

class OperationRunnerDecorator implements Client
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var OperationRunner
     */
    private $operationRunner;

    /**
     * @param Client          $client
     * @param OperationRunner $operationRunner
     */
    public function __construct(Client $client, OperationRunner $operationRunner)
    {
        $this->client = $client;
        $this->operationRunner = $operationRunner;
    }

    /**
     * {@inheritdoc}
     */
    public function create(LogNode $log, Log $parent = null)
    {
        $operation = new Callback(function () use ($log, $parent) {
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
        $operation = new Callback(function () use ($log, $status) {
            return $this->client->updateStatus($log, $status);
        });

        $this->operationRunner->run($operation);

        return $operation->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function archive(Log $log)
    {
        $operation = new Callback(function () use ($log) {
            return $this->client->archive($log);
        });

        $this->operationRunner->run($operation);

        return $operation->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function patch(Log $log, array $patch)
    {
        $operation = new Callback(function () use ($log, $patch) {
            return $this->client->patch($log, $patch);
        });

        $this->operationRunner->run($operation);

        return $operation->getResult();
    }
}
