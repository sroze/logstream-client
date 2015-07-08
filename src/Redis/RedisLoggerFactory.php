<?php

namespace ContinuousPipe\LogStream\Redis;

use ContinuousPipe\LogStream\LoggerFactory;
use ContinuousPipe\LogStream\LogRelatedObject;
use ContinuousPipe\LogStream\RealTime\RealTimePublisher;
use Predis\Client;

class RedisLoggerFactory implements LoggerFactory
{
    /**
     * @var Client
     */
    private $redisClient;
    /**
     * @var ListNamingStrategy
     */
    private $listNamingStrategy;

    public function __construct(Client $redisClient, ListNamingStrategy $listNamingStrategy)
    {
        $this->redisClient = $redisClient;
        $this->listNamingStrategy = $listNamingStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function createLogger(LogRelatedObject $object)
    {
        return new RedisLogger($this->redisClient, $this->listNamingStrategy, $object);
    }
}
