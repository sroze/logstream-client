<?php

namespace LogStream;

interface Client
{
    /**
     * Create a new log with the given parent.
     *
     * @param LogNode $log
     * @param Log     $parent
     *
     * @return Log
     */
    public function create(LogNode $log, Log $parent = null);

    /**
     * @param Log    $log
     * @param string $status
     *
     * @return Log
     */
    public function updateStatus(Log $log, $status);
}
