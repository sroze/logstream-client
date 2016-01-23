<?php

namespace LogStream\Node;

class VoidNode implements Node
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [];
    }
}
