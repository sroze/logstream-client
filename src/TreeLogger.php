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
        return $this->client->updateStatus($this->parent, Log::RUNNING);
    }

    /**
     * {@inheritdoc}
     */
    public function getLog()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function success()
    {
        return $this->client->updateStatus($this->parent, Log::SUCCESS);
    }

    /**
     * {@inheritdoc}
     */
    public function failure()
    {
        return $this->client->updateStatus($this->parent, Log::FAILURE);
    }
}
