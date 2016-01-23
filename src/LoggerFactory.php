<?php

namespace LogStream;

interface LoggerFactory
{
    /**
     * @return Logger
     */
    public function create();

    /**
     * @param Log $log
     *
     * @return Logger
     */
    public function from(Log $log);

    /**
     * Create a logger from the ID of the parent.
     *
     * @param string $identifier
     *
     * @return Logger
     */
    public function fromId($identifier);
}
