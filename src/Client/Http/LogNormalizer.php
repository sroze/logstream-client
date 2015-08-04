<?php

namespace LogStream\Client\Http;

use LogStream\LogNode;

class LogNormalizer
{
    /**
     * Normalize a LogNode object.
     *
     * @param LogNode $log
     *
     * @return array
     */
    public function normalize(LogNode $log)
    {
        return $log->jsonSerialize();
    }
}
