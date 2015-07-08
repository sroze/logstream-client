<?php

namespace ContinuousPipe\LogStream\RealTime;

use ContinuousPipe\LogStream\LogRelatedObject;

interface RealTimeSubscriber
{
    /**
     * Subscribe to event related to the given deployment.
     *
     * It actually return a generator, not an array.
     *
     * @param LogRelatedObject $relatedObject
     *
     * @return Event[]
     */
    public function subscribe(LogRelatedObject $relatedObject);
}
