<?php

namespace ContinuousPipe\LogStream\RealTime;

use ContinuousPipe\LogStream\LogRelatedObject;

interface Event
{
    /**
     * @return LogRelatedObject
     */
    public function getRelatedObject();
}
