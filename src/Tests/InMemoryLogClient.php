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
     * @param Client\Normalizer\LogNormalizer $logNormalizer
     */
    public function __construct(Client\Normalizer\LogNormalizer $logNormalizer)
    {
        $this->logNormalizer = $logNormalizer;
    }

    /**
     * @var Log[]
     */
    private $logs;

    /**
     * {@inheritdoc}
     */
    public function create(Log $log)
    {
        $normalized = $this->logNormalizer->normalize($log);
        if (!array_key_exists('_id', $normalized)) {
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

        $this->logs[$normalized['_id']] = $normalized;

        return $this->logNormalizer->denormalize($normalized);
    }
}
