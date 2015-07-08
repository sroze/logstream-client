<?php

namespace ContinuousPipe\LogStream\Redis;

use ContinuousPipe\LogStream\LogAggregator;
use ContinuousPipe\LogStream\LogRelatedObject;
use ContinuousPipe\LogStream\Serializer\LogSerializer;
use Predis\Client;

class RedisLogAggregator implements LogAggregator
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

    public function getLogsFor(LogRelatedObject $relatedObject)
    {
        $listName = $this->listNamingStrategy->getListName($relatedObject);
        $rawLogs = $this->redisClient->lrange($listName, 0, -1);

        $logs = [];
        foreach ($rawLogs as $rawLog) {
            $logs[] = $this->logSerializer->deserialize($rawLog);
        }

        return $logs;
    }
}
