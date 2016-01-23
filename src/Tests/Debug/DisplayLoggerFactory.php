<?php

namespace LogStream\Tests\Debug;

use LogStream\Client\Normalizer\LogNormalizer;
use LogStream\Log;
use LogStream\LoggerFactory;

class DisplayLoggerFactory implements LoggerFactory
{
    /**
     * @var LoggerFactory
     */
    private $factory;

    /**
     * @var LogNormalizer
     */
    private $logNormalizer;

    /**
     * @param LoggerFactory $factory
     * @param LogNormalizer $logNormalizer
     */
    public function __construct(LoggerFactory $factory, LogNormalizer $logNormalizer)
    {
        $this->factory = $factory;
        $this->logNormalizer = $logNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new DisplayLogger($this->factory->create(), $this->logNormalizer);
    }

    /**
     * {@inheritdoc}
     */
    public function from(Log $parent)
    {
        return new DisplayLogger($this->factory->from($parent), $this->logNormalizer);
    }

    /**
     * {@inheritdoc}
     */
    public function fromId($parentId)
    {
        return new DisplayLogger($this->factory->fromId($parentId), $this->logNormalizer);
    }
}
