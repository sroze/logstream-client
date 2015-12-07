<?php

namespace LogStream;

use LogStream\Client\ClientException;

interface Client
{
    /**
     * Create a new log with the given parent.
     *
     * @param LogNode $log
     * @param Log     $parent
     *
     * @throws ClientException
     *
     * @return Log
     */
    public function create(LogNode $log, Log $parent = null);

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
