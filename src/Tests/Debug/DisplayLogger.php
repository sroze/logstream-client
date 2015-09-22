<?php

namespace LogStream\Tests\Debug;

use LogStream\Logger;
use LogStream\LogNode;

class DisplayLogger implements Logger
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function append(LogNode $log)
    {
        $log = $this->logger->append($log);

        $serialized = $log->jsonSerialize();
        echo sprintf('[%s] %s'."\n", $serialized['type'], $serialized['contents']);

        return $log;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        return $this->logger->start();
    }

    /**
     * {@inheritdoc}
     */
    public function success()
    {
        return $this->logger->success();
    }

    /**
     * {@inheritdoc}
     */
    public function failure()
    {
        return $this->logger->failure();
    }

    /**
     * {@inheritdoc}
     */
    public function getLog()
    {
        return $this->logger->getLog();
    }
}
