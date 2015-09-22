<?php

namespace LogStream\Node;

use LogStream\LogNode;

class Raw implements LogNode
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'type' => 'raw',
        ];
    }
}
