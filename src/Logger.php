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
     *
     * @return Log
     */
    public function start();

    /**
     * Update the log status to success.
     *
     * @return Log
     */
    public function success();

    /**
     * Update the log status to failure.
     *
     * @return Log
     */
    public function failure();

    /**
     * @return Log|null
     */
    public function getLog();
}
