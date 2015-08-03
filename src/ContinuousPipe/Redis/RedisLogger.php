<?php

namespace ContinuousPipe\LogStream\Redis;

use ContinuousPipe\LogStream\Log;
use ContinuousPipe\LogStream\Logger;
use ContinuousPipe\LogStream\LogRelatedObject;
use ContinuousPipe\LogStream\RealTime\RealTimePublisher;
use ContinuousPipe\LogStream\Serializer\LogSerializer;
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
    /**
     * @var LogSerializer
     */
    private $logSerializer;

    public function __construct(Client $redisClient, ListNamingStrategy $listNamingStrategy, LogSerializer $logSerializer, LogRelatedObject $relatedObject)
    {
        $this->redisClient = $redisClient;
        $this->listNamingStrategy = $listNamingStrategy;
        $this->relatedObject = $relatedObject;
        $this->logSerializer = $logSerializer;
    }

    public function log(Log $log)
    {
        $listName = $this->listNamingStrategy->getListName($this->relatedObject);

        $body = $this->logSerializer->serialize($log);
        $this->redisClient->rpush($listName, $body);
    }
}
