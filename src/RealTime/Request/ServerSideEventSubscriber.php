<?php

namespace ContinuousPipe\LogStream\RealTime\Request;

use ContinuousPipe\LogStream\LogRelatedObject;
use ContinuousPipe\LogStream\RealTime\RealTimeSubscriber;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Serializer\SerializerInterface;

class ServerSideEventSubscriber
{
    /**
     * @var RealTimeSubscriber
     */
    private $subscriber;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param RealTimeSubscriber  $subscriber
     * @param SerializerInterface $serializer
     */
    public function __construct(RealTimeSubscriber $subscriber, SerializerInterface $serializer)
    {
        $this->subscriber = $subscriber;
        $this->serializer = $serializer;
    }

    /**
     * @param LogRelatedObject $relatedObject
     *
     * @return StreamedResponse
     */
    public function getSubscribeResponse(LogRelatedObject $relatedObject)
    {
        return new StreamedResponse(function () use ($relatedObject) {
            $this->prepareStreamConnection();
            $this->subscribe($relatedObject);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
        ]);
    }

    /**
     * @param LogRelatedObject $relatedObject
     */
    public function subscribe(LogRelatedObject $relatedObject)
    {
        $generator = $this->subscriber->subscribe($relatedObject->getIdentifier());

        foreach ($generator as $event) {
            $message = $this->serializer->serialize($event, 'json');
            $this->sendData($message);

            // TODO Handle end of subscribing
        }
    }

    /**
     * Prepare stream event connection.
     */
    protected function prepareStreamConnection()
    {
        // Disable session lock
        session_write_close();

        // Fix Bug IE<10 with EventSource
        // @see https://github.com/Yaffle/EventSource#server-side-requirements
        echo ':'.str_repeat(' ', 2048)."\n"; // 2 kB padding for IE
        echo "retry: 2000\n";
    }

    /**
     * @param string $data
     */
    protected function sendData($data)
    {
        echo 'data: '.$data."\n\n";
        ob_flush();
        flush();
    }
}
