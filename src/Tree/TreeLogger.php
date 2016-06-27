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
