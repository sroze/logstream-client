<?php

namespace LogStream;

interface Log extends LogNode
{
    const RUNNING = 'running';
    const SUCCESS = 'success';
    const FAILURE = 'failure';

    /**
     * Is the log started ?
     *
     * @return bool
     */
    public function isStarted();

    /**
     * Return the unique identifier of the log.
     *
     * @return string
     */
    public function getId();

    /**
     * Get status of the log.
     *
     * @return string
     */
    public function getStatus();
}
