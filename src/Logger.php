<?php

namespace LogStream;

use LogStream\Node\Node;

interface Logger
{
    /**
     * @param Node $node
     *
     * @return Logger
     */
    public function child(Node $node);

    /**
     * Update the log status to running.
     *
     * @return Logger
     */
    public function updateStatus($status);

    /**
     * @return Log
     */
    public function getLog();
}
