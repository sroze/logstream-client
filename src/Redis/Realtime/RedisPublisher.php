<?php

namespace ContinuousPipe\LogStream\Redis\Realtime;

use ContinuousPipe\LogStream\RealTime\Event;
use ContinuousPipe\LogStream\RealTime\RealTimePublisher;
use ContinuousPipe\LogStream\Redis\ListNamingStrategy;
use Predis\Client;

class RedisPublisher implements RealTimePublisher
{
    /**
     * @var ListNamingStrategy
     */
    private $listNamingStrategy;
    /**
     * @var Client
     */
    private $redisClient;
    /**
     * @var AnonymousClassSerializer
     */
    private $serializer;

    /**
     * @param ListNamingStrategy       $listNamingStrategy
     * @param Client                   $redisClient
     * @param AnonymousClassSerializer $serializer
     */
    public function __construct(ListNamingStrategy $listNamingStrategy, Client $redisClient, AnonymousClassSerializer $serializer)
    {
        $this->listNamingStrategy = $listNamingStrategy;
        $this->redisClient = $redisClient;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(Event $event)
    {
        $listName = $this->listNamingStrategy->getListName($event->getRelatedObject());
        $body = $this->serializer->serialize($event);

        $this->redisClient->publish($listName, $body);
    }
}
