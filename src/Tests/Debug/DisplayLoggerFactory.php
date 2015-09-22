<?php

namespace LogStream\Tests\Debug;

use LogStream\Log;
use LogStream\LoggerFactory;

class DisplayLoggerFactory implements LoggerFactory
{
    /**
     * @var LoggerFactory
     */
    private $factory;

    /**
     * @param LoggerFactory $factory
     */
    public function __construct(LoggerFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new DisplayLogger($this->factory->create());
    }

    /**
     * {@inheritdoc}
     */
    public function from(Log $parent)
    {
        return new DisplayLogger($this->factory->from($parent));
    }

    /**
     * {@inheritdoc}
     */
    public function fromId($parentId)
    {
        return new DisplayLogger($this->factory->fromId($parentId));
    }
}
