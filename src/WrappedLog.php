<?php

namespace LogStream;

class WrappedLog implements Log
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $status;

    /**
     * @var LogNode
     */
    private $node;

    /**
     * @param LogNode $node
     */
    public function __construct(LogNode $node)
    {
        $this->node = $node;
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->status === Log::RUNNING;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'status' => $this->status,
        ] + $this->node->jsonSerialize();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
