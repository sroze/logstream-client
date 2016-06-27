<?php

namespace LogStream\Tests;

use LogStream\Client;
use LogStream\Client\ClientException;
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
        return $this->patch($log, [
            'status' => $status,
        ]);
    }

    /**
     * @return <string, array>
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * {@inheritdoc}
     */
    public function archive(Log $log)
    {
        return $log;
    }

    /**
     * {@inheritdoc}
     */
    public function patch(Log $log, array $patch)
    {
        $normalized = $this->logNormalizer->normalize($log);
        $patched = array_merge($normalized, $patch);

        if (array_key_exists($patched['_id'], $this->logs)) {
            $patched = array_merge($this->logs[$patched['_id']], $patched);
        }

        $this->logs[$patched['_id']] = $patched;

        return $this->logNormalizer->denormalize($patched);
    }
}
