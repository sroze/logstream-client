<?php

namespace LogStream\FaultTolerance;

use LogStream\Log;
use LogStream\LoggerFactory;
use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface
     */
    private $systemLogger;

    public function __construct(
        LoggerFactory $decoratedFactory,
        OperationRunner $operationRunner,
        LoggerInterface $systemLogger
    ) {
        $this->decoratedFactory = $decoratedFactory;
        $this->operationRunner = $operationRunner;
        $this->systemLogger = $systemLogger;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->operationRunner->run(new Callback(function() {
            return new FaultTolerantLogger(
                $this->decoratedFactory->create(),
                $this->operationRunner,
                $this->systemLogger
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
            $this->operationRunner,
            $this->systemLogger
        );
    }

    /**
     * {@inheritdoc}
     */
    public function fromId($identifier)
    {
        return new FaultTolerantLogger(
            $this->decoratedFactory->fromId($identifier),
            $this->operationRunner,
            $this->systemLogger
        );
    }
}
