<?php

namespace LogStream;

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
        $parent = $this->client->create(new Container());

        return $this->from($parent);
    }

    /**
     * {@inheritdoc}
     */
    public function from(Log $parent)
    {
        return new TreeLogger($this->client, $parent);
    }
}
