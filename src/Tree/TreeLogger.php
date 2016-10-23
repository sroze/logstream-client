<?php

namespace LogStream\Tree;

use LogStream\Client;
use LogStream\Log;
use LogStream\Logger;
use LogStream\Node\Node;
use LogStream\Node\Normalizer\NodeNormalizer;

class TreeLogger implements Logger
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var NodeNormalizer
     */
    private $nodeNormalizer;

    /**
     * @var Log
     */
    private $log;

    /**
     * @param Client $client
     * @param NodeNormalizer $nodeNormalizer
     * @param Log $log
     */
    public function __construct(Client $client, NodeNormalizer $nodeNormalizer, Log $log)
    {
        $this->client = $client;
        $this->nodeNormalizer = $nodeNormalizer;
        $this->log = $log;
    }

    /**
     * {@inheritdoc}
     */
    public function child(Node $node)
    {
        $child = $this->client->create(
            TreeLog::fromNodeAndParent($node, $this->log)
        );

        return new self($this->client, $this->nodeNormalizer, $child);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Node $node)
    {
        $this->log = $this->client->patch($this->log, $this->nodeNormalizer->normalize($node));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus($status)
    {
        $patch = [
            'status' => $status,
            $status.'At' => (new \DateTime())->format(\DateTime::ISO8601),
        ];

        $this->log = $this->client->patch($this->log, $patch);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLog()
    {
        return $this->log;
    }
}
