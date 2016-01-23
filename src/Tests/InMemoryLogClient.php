<?php

namespace LogStream\Tests;

use LogStream\Client;
use LogStream\Log;

class InMemoryLogClient implements Client
{
    /**
     * @var Client\Normalizer\LogNormalizer
     */
    private $logNormalizer;

    /**
     * @var <string, array>
     */
    private $logs;

    /**
     * @param Client\Normalizer\LogNormalizer $logNormalizer
     */
    public function __construct(Client\Normalizer\LogNormalizer $logNormalizer)
    {
        $this->logNormalizer = $logNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Log $log)
    {
        $normalized = $this->logNormalizer->normalize($log);
        if (!array_key_exists('_id', $normalized) || empty($normalized['_id'])) {
            $normalized['_id'] = uniqid();
        }

        $this->logs[$normalized['_id']] = $normalized;

        return $this->logNormalizer->denormalize($normalized);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus(Log $log, $status)
    {
        $normalized = $this->logNormalizer->normalize($log);
        $normalized['status'] = $status;

        if (array_key_exists($normalized['_id'], $this->logs)) {
            $normalized = array_merge($this->logs[$normalized['_id']], $normalized);
        }

        $this->logs[$normalized['_id']] = $normalized;

        return $this->logNormalizer->denormalize($normalized);
    }

    /**
     * @return <string, array>
     */
    public function getLogs()
    {
        return $this->logs;
    }
}
