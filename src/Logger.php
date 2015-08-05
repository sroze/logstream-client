<?php

namespace LogStream;

interface Logger
{
    /**
     * @param LogNode $log
     *
     * @return Log
     */
    public function append(LogNode $log);

    /**
     * Update the log status to running.
     */
    public function start();

    /**
     * Update the log status to success.
     */
    public function success();

    /**
     * Update the log status to failure.
     */
    public function failure();

    /**
     * @return Log|null
     */
    public function getLog();
}
