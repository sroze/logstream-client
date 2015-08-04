<?php

namespace LogStream;

class TreeLogger implements Logger
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Log
     */
    private $parent;

    /**
     * @param Client $client
     * @param Log    $parent
     */
    public function __construct(Client $client, Log $parent)
    {
        $this->client = $client;
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function append(LogNode $log)
    {
        return $this->client->create($log, $this->parent);
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $this->client->updateStatus($this->parent, Log::RUNNING);
    }

    /**
     * @return Log
     */
    public function getLog()
    {
        return $this->parent;
    }
}
