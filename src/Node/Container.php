<?php

namespace LogStream\Node;

class Container implements Node
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return ['type' => 'text'];
    }
}
