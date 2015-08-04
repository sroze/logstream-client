<?php

namespace LogStream;

class Logger
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
     * @param LogNode $log
     * @return Log
     */
    public function append(LogNode $log)
    {
        return $this->client->create($log, $this->parent);
    }

    /**
     * Update the log status to running.
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
