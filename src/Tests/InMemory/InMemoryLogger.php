<?php

namespace LogStream\Tests\InMemory;

use LogStream\EmptyLogger;
use LogStream\Log;
use LogStream\Logger;
use LogStream\LogNode;
use LogStream\Tests\MutableWrappedLog;

class InMemoryLogger implements Logger
{
    /**
     * @var EmptyLogger
     */
    private $emptyLogger;

    /**
     * @var InMemoryLogStore
     */
    private $logStore;

    /**
     * @param EmptyLogger      $emptyLogger
     * @param InMemoryLogStore $logStore
     */
    public function __construct(EmptyLogger $emptyLogger, InMemoryLogStore $logStore)
    {
        $this->emptyLogger = $emptyLogger;
        $this->logStore = $logStore;
    }

    /**
     * {@inheritdoc}
     */
    public function append(LogNode $log)
    {
        $log = $this->emptyLogger->append($log);
        $log = new MutableWrappedLog($log->getId(), $log->getNode(), $log->getStatus());
        $this->logStore->store($log, $this->emptyLogger->getLog());

        return $log;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        return $this->updatesStatus(Log::RUNNING);
    }

    /**
     * {@inheritdoc}
     */
    public function getLog()
    {
        return $this->emptyLogger->getLog();
    }

    /**
     * {@inheritdoc}
     */
    public function success()
    {
        return $this->updatesStatus(Log::SUCCESS);
    }

    /**
     * {@inheritdoc}
     */
    public function failure()
    {
        return $this->updatesStatus(Log::FAILURE);
    }

    /**
     * @param string $status
     *
     * @return Log|null
     */
    private function updatesStatus($status)
    {
        $log = $this->emptyLogger->getLog();

        if ($log instanceof MutableWrappedLog) {
            $log->setStatus($status);
        } else {
            throw new \RuntimeException('Non-mutable log found');
        }

        return $log;
    }
}
