<?php

namespace LogStream;

interface LoggerFactory
{
    /**
     * @return Logger
     */
    public function create();

    /**
     * @param Log $parent
     *
     * @return Logger
     */
    public function from(Log $parent);

    /**
     * Create a logger from the ID of the parent.
     *
     * @param string $parentId
     *
     * @return Logger
     */
    public function fromId($parentId);
}
