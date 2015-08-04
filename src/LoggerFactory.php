<?php

namespace LogStream;

use LogStream\Node\Container;

class LoggerFactory
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
     * @return Logger
     */
    public function create()
    {
        $parent = $this->client->create(new Container());

        return new Logger($this->client, $parent);
    }
}
