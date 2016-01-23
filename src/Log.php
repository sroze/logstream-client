<?php

namespace LogStream;

use LogStream\Node\Node;

interface Log
{
    const PENDING = 'pending';
    const RUNNING = 'running';
    const SUCCESS = 'success';
    const FAILURE = 'failure';

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

    /**
     * Get parent identifier of the given log.
     *
     * @return string|null
     */
    public function getParentIdentifier();

    /**
     * @return Node
     */
    public function getNode();
}
