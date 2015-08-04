<?php

namespace LogStream\Client;

use GuzzleHttp\Message\ResponseInterface;
use LogStream\Client;
use LogStream\Log;
use GuzzleHttp\Client as GuzzleClient;
use LogStream\LogNode;
use LogStream\WrappedLog;

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
     * @param GuzzleClient       $httpClient
     * @param Http\LogNormalizer $logNormalizer
     * @param string             $baseUrl
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
    public function create(LogNode $log, Log $parent = null)
    {
        $normalized = $this->logNormalizer->normalize($log);

        if (null !== $parent) {
            $normalized['parent'] = $parent->getId();
        }

        $response = $this->httpClient->post($this->baseUrl.'/api/logs', [
            'json' => $normalized,
        ]);

        return $this->getWrappedResponseLog($response, $log);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus(Log $log, $status)
    {
        $response = $this->httpClient->put($this->baseUrl.'/api/logs/'.$log->getId(), [
            'json' => [
                'status' => $status,
            ],
        ]);

        return $this->getWrappedResponseLog($response, $log);
    }

    /**
     * @param ResponseInterface $response
     * @param LogNode $logNode
     * @return WrappedLog
     * @throws ClientException
     */
    private function getWrappedResponseLog(ResponseInterface $response, LogNode $logNode)
    {
        $json = $response->json();
        if ($json['status'] != 'success') {
            throw new ClientException('Response is not successful');
        }

        $data = $json['data'];
        return new WrappedLog($data['_id'], $logNode, array_key_exists('status', $data) ? $data['status'] : null);
    }
}
