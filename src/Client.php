<?php

namespace LogStream;

interface Client
{
    /**
     * Create a new log with the given parent.
     *
     * @param Log $log
     * @param Log $parent
     * @return Log
     */
    public function create(Log $log, Log $parent = null);

    /**
     * @param Log    $log
     * @param string $status
     * @return Log
     */
    public function updateStatus(Log $log, $status);
}
