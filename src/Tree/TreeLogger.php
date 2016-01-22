<?php

namespace LogStream\Tree;

use LogStream\Client;
use LogStream\Log;
use LogStream\Logger;
use LogStream\Node\Node;

class TreeLogger implements Logger
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Log
     */
    private $log;

    /**
     * @param Client $client
     * @param Log    $log
     */
    public function __construct(Client $client, Log $log)
    {
        $this->client = $client;
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

        return new self($this->client, $child);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus($status)
    {
        $this->log = $this->client->updateStatus($this->log, $status);

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
