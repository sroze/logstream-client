<?php

namespace ContinuousPipe\LogStream\Redis;

use ContinuousPipe\LogStream\LoggerFactory;
use ContinuousPipe\LogStream\LogRelatedObject;
use ContinuousPipe\LogStream\Serializer\LogSerializer;
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
    /**
     * @var LogSerializer
     */
    private $logSerializer;

    public function __construct(Client $redisClient, ListNamingStrategy $listNamingStrategy, LogSerializer $logSerializer)
    {
        $this->redisClient = $redisClient;
        $this->listNamingStrategy = $listNamingStrategy;
        $this->logSerializer = $logSerializer;
    }

    /**
     * {@inheritdoc}
     */
    public function createLogger(LogRelatedObject $object)
    {
        return new RedisLogger($this->redisClient, $this->listNamingStrategy, $this->logSerializer, $object);
    }
}
