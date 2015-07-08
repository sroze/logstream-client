<?php

namespace ContinuousPipe\LogStream;

interface LogRelatedObject
{
    /**
     * Identifier of this object related to log.
     *
     * @return string
     */
    public function getIdentifier();
}
