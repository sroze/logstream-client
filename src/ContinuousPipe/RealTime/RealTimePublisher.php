<?php

namespace ContinuousPipe\LogStream\RealTime;

interface RealTimePublisher
{
    /**
     * Publish an event.
     *
     * @param Event $event
     */
    public function publish(Event $event);
}
