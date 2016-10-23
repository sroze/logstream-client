<?php

namespace LogStream\Node\Normalizer;

use LogStream\Node\Complex;
use LogStream\Node\Container;
use LogStream\Node\Node;
use LogStream\Node\Raw;
use LogStream\Node\Text;
use LogStream\Node\VoidNode;
use Psr\Log\LoggerInterface;

class BaseNormalizer implements NodeNormalizer
{
    /**
     * @var null|LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

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

        return $node->jsonSerialize();
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

        }

        if (null !== $this->logger) {
            $this->logger->warning('Got a log node without known type', [
                'type' => $array['type'],
                'node' => $array
            ]);
        }

        return new Complex($array['type'], $array);
    }
}
