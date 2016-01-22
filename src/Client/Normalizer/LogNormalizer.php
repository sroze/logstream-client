<?php

namespace LogStream\Client\Normalizer;

use LogStream\Log;

interface LogNormalizer
{
    /**
     * Normalize the given log.
     *
     * @param Log $log
     *
     * @return array
     */
    public function normalize(Log $log);

    /**
     * @param array $json
     *
     * @return Log
     */
    public function denormalize(array $json);
}
