<?php

namespace LogStream\Client\Http;

use LogStream\Log;

class LogNormalizer
{
    /**
     * Normalize a Log object.
     *
     * @param Log $log
     *
     * @return array
     */
    public function normalize(Log $log)
    {
        return $log->jsonSerialize();
    }
}
