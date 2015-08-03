<?php

namespace ContinuousPipe\LogStream\Serializer;

use ContinuousPipe\LogStream\Log;

interface LogSerializer
{
    /**
     * @param Log $log
     *
     * @return string
     */
    public function serialize(Log $log);

    /**
     * @param string $raw
     *
     * @return Log
     */
    public function deserialize($raw);
}
