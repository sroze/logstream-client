<?php

namespace LogStream;

use LogStream\Node\Container;

class EmptyLogger implements Logger
{
    /**
     * {@inheritdoc}
     */
    public function append(LogNode $log)
    {
        return new WrappedLog(uniqid(), new Container());
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
    }
}
