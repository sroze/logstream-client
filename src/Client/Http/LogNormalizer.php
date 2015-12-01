<?php

namespace LogStream\Client\Http;

use LogStream\Log;
use LogStream\LogNode;

class LogNormalizer
{
    /**
     * Normalize a LogNode object.
     *
     * @param LogNode $log
     * @param Log $parent
     *
     * @return array
     */
    public function normalize(LogNode $log, Log $parent = null)
    {
        $normalized = $log->jsonSerialize();

        if (null !== $parent) {
            $normalized['parent'] = $parent->getId();
        }

        return $normalized;
    }
}
