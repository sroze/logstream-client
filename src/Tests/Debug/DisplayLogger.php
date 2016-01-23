<?php

namespace LogStream\Tests\Debug;

use LogStream\Client\Normalizer\LogNormalizer;
use LogStream\Log;
use LogStream\Logger;
use LogStream\LogNode;
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
     * @param Logger $logger
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
        $normalized = $this->logNormalizer->normalize($logger->getLog());

        echo sprintf('[%s] %s'."\n", $normalized['type'], isset($normalized['contents']) ? $normalized['contents'] : '[no content]');

        return $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus($status)
    {
        return $this->logger->updateStatus($status);
    }

    /**
     * {@inheritdoc}
     */
    public function getLog()
    {
        return $this->logger->getLog();
    }
}
