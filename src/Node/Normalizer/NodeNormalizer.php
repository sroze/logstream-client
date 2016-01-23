<?php

namespace LogStream\Node\Normalizer;

use LogStream\Node\Node;

interface NodeNormalizer
{
    /**
     * @param Node $node
     *
     * @return array
     */
    public function normalize(Node $node);

    /**
     * @param array $array
     *
     * @return Node
     */
    public function denormalize(array $array);
}
