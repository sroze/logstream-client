<?php

namespace LogStream\FaultTolerance;

use LogStream\Log;
use LogStream\LoggerFactory;
use Tolerance\Operation\Callback;
use Tolerance\Operation\Runner\OperationRunner;

class FaultTolerantLoggerFactory implements LoggerFactory
{
    /**
     * @var LoggerFactory
     */
    private $decoratedFactory;

    /**
     * @var OperationRunner
     */
    private $operationRunner;

    /**
     * @param LoggerFactory $decoratedFactory
     * @param OperationRunner $operationRunner
     */
    public function __construct(LoggerFactory $decoratedFactory, OperationRunner $operationRunner)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->operationRunner = $operationRunner;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->operationRunner->run(new Callback(function() {
            return new FaultTolerantLogger(
                $this->decoratedFactory->create(),
                $this->operationRunner
            );
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function from(Log $log)
    {
        return new FaultTolerantLogger(
            $this->decoratedFactory->from($log),
            $this->operationRunner
        );
    }

    /**
     * {@inheritdoc}
     */
    public function fromId($identifier)
    {
        return new FaultTolerantLogger(
            $this->decoratedFactory->fromId($identifier),
            $this->operationRunner
        );
    }
}
