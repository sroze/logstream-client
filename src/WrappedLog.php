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
     * @param string  $id
     * @param LogNode $node
     * @param string  $status
     */
    public function __construct($id, LogNode $node, $status = null)
    {
        $this->id = $id;
        $this->node = $node;
        $this->status = $status;
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

    /**
     * @return LogNode
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return null|string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
