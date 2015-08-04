<?php

namespace LogStream\Node;

use LogStream\LogNode;

class Container implements LogNode
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return ['type' => 'text'];
    }
}
