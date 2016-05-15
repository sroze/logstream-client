<?php

namespace LogStream\Node\Normalizer;

use LogStream\Node\Container;
use LogStream\Node\Node;
use LogStream\Node\Raw;
use LogStream\Node\Text;
use LogStream\Node\VoidNode;

class BaseNormalizer implements NodeNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize(Node $node)
    {
        if ($node instanceof Container) {
            return ['type' => 'text'];
        } elseif ($node instanceof Raw) {
            return ['type' => 'raw'];
        } elseif ($node instanceof Text) {
            return [
                'type' => 'text',
                'contents' => $node->getText(),
            ];
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $array)
    {
        if (!array_key_exists('type', $array)) {
            return new VoidNode($array);
        }

        switch ($array['type']) {
            case 'raw':
                return new Raw();
            case 'text':
                if (array_key_exists('contents', $array)) {
                    return new Text($array['contents']);
                }

                return new Container();
        }

        throw new \RuntimeException('No type of node found');
    }
}
