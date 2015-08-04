<?php

namespace LogStream\Client;

use LogStream\Client;
use LogStream\Log;
use GuzzleHttp\Client as GuzzleClient;

class HttpClient implements Client
{
    /**
     * @var GuzzleClient
     */
    private $httpClient;

    /**
     * @var Http\LogNormalizer
     */
    private $logNormalizer;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @param GuzzleClient $httpClient
     * @param Http\LogNormalizer $logNormalizer
     * @param string $baseUrl
     */
    public function __construct(GuzzleClient $httpClient, Client\Http\LogNormalizer $logNormalizer, $baseUrl)
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = $baseUrl;
        $this->logNormalizer = $logNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Log $log, Log $parent = null)
    {
        $normalized = $this->logNormalizer->normalize($log);
        if (null !== $parent) {
            $normalized['parent'] = $parent->getId();
        }

        $this->httpClient->post($this->baseUrl.'/api/logs', [
            'json' => $normalized
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus(Log $log, $status)
    {
        $this->httpClient->put($this->baseUrl.'/api/logs/'.$log->getId(), [
            'json' => [
                'status' => $status
            ]
        ]);
    }
}
