<?php

namespace ContinuousPipe\LogStream\Redis\Realtime;

use ContinuousPipe\LogStream\LogRelatedObject;
use ContinuousPipe\LogStream\RealTime\RealTimeSubscriber;
use ContinuousPipe\LogStream\Redis\ListNamingStrategy;
use Predis\Client;

class RedisSubscriber implements RealTimeSubscriber
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
    public function subscribe(LogRelatedObject $relatedObject)
    {
        $listName = $this->listNamingStrategy->getListName($relatedObject);

        $consumer = $this->redisClient->pubSubLoop();
        $consumer->subscribe($listName);

        foreach ($consumer as $message) {
            if ('message' !== $message->kind) {
                continue;
            }

            yield $this->serializer->deserialize($message->payload, Event::class);
        }
    }
}
