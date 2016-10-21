<?php

namespace LogStream\Tree;

use LogStream\Client;
use LogStream\Log;
use LogStream\LoggerFactory;
use LogStream\Node\Container;
use LogStream\Node\Normalizer\NodeNormalizer;

class TreeLoggerFactory implements LoggerFactory
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
     * @param Client $client
     * @param NodeNormalizer $nodeNormalizer
     */
    public function __construct(Client $client, NodeNormalizer $nodeNormalizer)
    {
        $this->client = $client;
        $this->nodeNormalizer = $nodeNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $log = $this->client->create(
            TreeLog::fromNode(new Container()
        ));

        return $this->from($log);
    }

    /**
     * {@inheritdoc}
     */
    public function fromId($identifier)
    {
        return $this->from(TreeLog::fromId($identifier));
    }

    /**
     * {@inheritdoc}
     */
    public function from(Log $log)
    {
        return new TreeLogger($this->client, $this->nodeNormalizer, $log);
    }
}
