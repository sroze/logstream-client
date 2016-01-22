<?php

namespace LogStream\Tree;

use LogStream\Client;
use LogStream\Log;
use LogStream\LoggerFactory;
use LogStream\Node\Container;

class TreeLoggerFactory implements LoggerFactory
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
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
        return new TreeLogger($this->client, $log);
    }
}
