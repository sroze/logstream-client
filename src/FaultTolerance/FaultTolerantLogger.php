<?php

namespace LogStream\FaultTolerance;

use LogStream\Client\ClientException;
use LogStream\Logger;
use LogStream\Node\Node;
use Psr\Log\LoggerInterface;
use Tolerance\Operation\Callback;
use Tolerance\Operation\Runner\OperationRunner;

class FaultTolerantLogger implements Logger
{
    /**
     * @var Logger
     */
    private $decoratedLogger;

    /**
     * @var OperationRunner
     */
    private $operationRunner;

    /**
     * @var LoggerInterface
     */
    private $systemLogger;

    /**
     * @param Logger $decoratedLogger
     * @param OperationRunner $operationRunner
     * @param LoggerInterface $systemLogger
     */
    public function __construct(Logger $decoratedLogger, OperationRunner $operationRunner, LoggerInterface $systemLogger)
    {
        $this->decoratedLogger = $decoratedLogger;
        $this->operationRunner = $operationRunner;
        $this->systemLogger = $systemLogger;
    }

    /**
     * {@inheritdoc}
     */
    public function child(Node $node)
    {
        return $this->operationRunner->run(new Callback(function() use ($node) {
            return $this->wrap($this->decoratedLogger->child($node));
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function update(Node $node)
    {
        try {
            return $this->operationRunner->run(new Callback(function () use ($node) {
                return $this->wrap($this->decoratedLogger->update($node));
            }));
        } catch (ClientException $e) {
            $this->systemLogger->warning('Cannot update LogStream log', [
                'exception' => $e,
            ]);

            return $this;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus($status)
    {
        try {
            return $this->operationRunner->run(new Callback(function () use ($status) {
                return $this->wrap($this->decoratedLogger->updateStatus($status));
            }));
        } catch (ClientException $e) {
            $this->systemLogger->warning('Cannot update LogStream log', [
                'exception' => $e,
            ]);

            return $this;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLog()
    {
        return $this->decoratedLogger->getLog();
    }

    /**
     * @param Logger $logger
     *
     * @return Logger
     */
    private function wrap(Logger $logger) : Logger
    {
        return new self(
            $logger,
            $this->operationRunner,
            $this->systemLogger
        );
    }

    /**
     * @return Logger
     */
    public function getDecoratedLogger(): Logger
    {
        return $this->decoratedLogger;
    }
}
