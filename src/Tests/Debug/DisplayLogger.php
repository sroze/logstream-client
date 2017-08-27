<?php

namespace LogStream\Tests\Debug;

use LogStream\Client\Normalizer\LogNormalizer;
use LogStream\Log;
use LogStream\Logger;
use LogStream\Node\Node;

class DisplayLogger implements Logger
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var LogNormalizer
     */
    private $logNormalizer;

    /**
     * @param Logger        $logger
     * @param LogNormalizer $logNormalizer
     */
    public function __construct(Logger $logger, LogNormalizer $logNormalizer)
    {
        $this->logger = $logger;
        $this->logNormalizer = $logNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function child(Node $node)
    {
        $logger = $this->logger->child($node);
        $log = $logger->getLog();

        $this->display($log);

        return new self($logger, $this->logNormalizer);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Node $node)
    {
        return $this->logger->update($node);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus($status)
    {
        $logger = $this->logger->updateStatus($status);

        $this->display($logger->getLog());

        return new self($logger, $this->logNormalizer);
    }

    /**
     * {@inheritdoc}
     */
    public function getLog()
    {
        return $this->logger->getLog();
    }

    /**
     * @param Log $log
     */
    private function display(Log $log)
    {
        $normalized = $this->logNormalizer->normalize($log);

        echo sprintf('[%s] [%s] %s' . "\n", isset($normalized['status']) ? $normalized['status'] : '?', isset($normalized['type']) ? $normalized['type'] : '?type', isset($normalized['contents']) ? $normalized['contents'] : '[no content]');
    }
}
