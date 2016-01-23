<?php

namespace LogStream;

use LogStream\Client\ClientException;

interface Client
{
    /**
     * Create a new log with the given parent.
     *
     * @param Log $log
     *
     * @throws ClientException
     *
     * @return Log
     */
    public function create(Log $log);

    /**
     * @param Log    $log
     * @param string $status
     *
     * @throws ClientException
     *
     * @return Log
     */
    public function updateStatus(Log $log, $status);
}
