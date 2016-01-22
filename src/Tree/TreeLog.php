<?php

namespace LogStream\Tree;

use LogStream\Log;
use LogStream\Node\Node;

class TreeLog implements Log
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $status;

    /**
     * @var Node
     */
    private $node;

    /**
     * @var Log
     */
    private $parent;

    /**
     * @param string $identifier
     * @param Node   $node
     * @param Log    $parent
     * @param null   $status
     *
     * @return TreeLog
     */
    public static function existing($identifier, Node $node, Log $parent = null, $status = null)
    {
        $log = new self();
        $log->identifier = $identifier;
        $log->node = $node;
        $log->parent = $parent;
        $log->status = $status;

        return $log;
    }

    /**
     * @param Node $node
     * @param Log  $parent
     *
     * @return TreeLog
     */
    public static function fromNodeAndParent(Node $node, Log $parent)
    {
        $log = new self();
        $log->node = $node;
        $log->parent = $parent;

        return $log;
    }

    /**
     * @param Node $node
     *
     * @return TreeLog
     */
    public static function fromNode(Node $node)
    {
        $log = new self();
        $log->node = $node;

        return $log;
    }

    /**
     * @param string $identifier
     *
     * @return TreeLog
     */
    public static function fromId($identifier)
    {
        $log = new self();
        $log->identifier = $identifier;

        return $log;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentIdentifier()
    {
        return null !== $this->parent ? $this->parent->getId() : null;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }
}
