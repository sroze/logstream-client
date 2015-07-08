<?php

namespace ContinuousPipe\LogStream\Redis;

use ContinuousPipe\LogStream\Log;
use ContinuousPipe\LogStream\Logger;
use ContinuousPipe\LogStream\LogRelatedObject;
use ContinuousPipe\LogStream\RealTime\RealTimePublisher;
use Predis\Client;

class RedisLogger implements Logger
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
     * @var LogRelatedObject
     */
    private $relatedObject;

    public function __construct(Client $redisClient, ListNamingStrategy $listNamingStrategy, LogRelatedObject $relatedObject)
    {
        $this->redisClient = $redisClient;
        $this->listNamingStrategy = $listNamingStrategy;
        $this->relatedObject = $relatedObject;
    }

    public function log(Log $log)
    {
        $listName = $this->listNamingStrategy->getListName($this->relatedObject);

        // TODO serialize
        $body = $log->getMessage();
        $this->redisClient->rpush($listName, $body);
    }
}
