<?php

namespace ContinuousPipe\LogStream;

interface LogAggregator
{
    /**
     * Get logs for a given object.
     *
     * @param LogRelatedObject $relatedObject
     *
     * @return Log[]
     */
    public function getLogsFor(LogRelatedObject $relatedObject);
}
