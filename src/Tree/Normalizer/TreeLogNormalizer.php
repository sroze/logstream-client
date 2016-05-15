<?php

namespace LogStream\Tree\Normalizer;

use LogStream\Client\Normalizer\LogNormalizer;
use LogStream\Log;
use LogStream\Node\Normalizer\NodeNormalizer;
use LogStream\Tree\TreeLog;
use PhpSpec\Exception\Exception;

class TreeLogNormalizer implements LogNormalizer
{
    /**
     * @var NodeNormalizer
     */
    private $nodeNormalizer;

    /**
     * @param NodeNormalizer $nodeNormalizer
     */
    public function __construct(NodeNormalizer $nodeNormalizer)
    {
        $this->nodeNormalizer = $nodeNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Log $log)
    {
        if (!$log instanceof TreeLog) {
            return [];
        } elseif (null === $log->getNode()) {
            throw new Exception('The log do not contains any node and wants to be serialized?');
        }

        return array_merge($this->nodeNormalizer->normalize($log->getNode()), [
            '_id' => $log->getId(),
            'parent' => $log->getParentIdentifier(),
            'status' => $log->getStatus(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $json)
    {
        return TreeLog::existing(
            $json['_id'],
            $this->nodeNormalizer->denormalize($json),
            $this->denormalizeParent($json),
            $this->denormalizeStatus($json)
        );
    }

    /**
     * @param array $json
     *
     * @return string
     */
    private function denormalizeStatus(array $json)
    {
        return array_key_exists('status', $json) ? $json['status'] : null;
    }

    /**
     * @param array $json
     *
     * @return TreeLog|null
     */
    private function denormalizeParent($json)
    {
        if (array_key_exists('parent', $json)) {
            return TreeLog::fromId($json['parent']);
        }

        return;
    }
}
