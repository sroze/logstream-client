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
}
