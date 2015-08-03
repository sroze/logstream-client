<?php

namespace ContinuousPipe\LogStream;

interface LoggerFactory
{
    /**
     * Create a logger for a given object.
     *
     * @param LogRelatedObject $object
     *
     * @return Logger
     */
    public function createLogger(LogRelatedObject $object);
}
