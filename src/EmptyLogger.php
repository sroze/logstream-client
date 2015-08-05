<?php

namespace LogStream;

class EmptyLogger implements Logger
{
    /**
     * @var Log
     */
    private $parent;

    /**
     * @param Log $parent
     */
    public function __construct(Log $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function append(LogNode $log)
    {
        return new WrappedLog(uniqid(), $log);
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getLog()
    {
        return $this->parent;
    }
}
