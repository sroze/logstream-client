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
}
