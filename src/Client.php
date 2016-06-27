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
     * @deprecated This should not be the client's responsibility, but the Logger.
     *             Uses the `patch` method to update an existing log instead.
     *
     * @return Log
     */
    public function updateStatus(Log $log, $status);

    /**
     * @param Log $log
     * @param array $patch
     *
     * @throws ClientException
     *
     * @return Log
     */
    public function patch(Log $log, array $patch);

    /**
     * Archive this log.
     *
     * @param Log $log
     *
     * @throws ClientException
     *
     * @return Log
     */
    public function archive(Log $log);
}
