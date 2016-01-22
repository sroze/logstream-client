<?php

namespace LogStream\Node;

class Raw implements Node
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
