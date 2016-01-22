<?php

namespace LogStream\Client;

use GuzzleHttp\Exception\AdapterException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use LogStream\Client;
use LogStream\Log;
use GuzzleHttp\Client as GuzzleClient;
use LogStream\LogNode;
use LogStream\WrappedLog;

class HttpClient implements Client
{
    /**
     * @var \LogStream\Client\LogNormalizer
     */
    private $logNormalizer;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @param GuzzleClient       $httpClient
     * @param \LogStream\Client\LogNormalizer $logNormalizer
     * @param string             $baseUrl
     */
    public function __construct(GuzzleClient $httpClient, Client\LogNormalizer $logNormalizer, $baseUrl)
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
        $normalized = $this->logNormalizer->normalize($log, $parent);

        try {
            $response = $this->httpClient->post($this->baseUrl . '/api/logs', [
                'json' => $normalized,
            ]);
        } catch (AdapterException $e) {
            throw new ClientException('Unable to create log', $e->getCode(), $e);
        } catch (RequestException $e) {
            throw new ClientException('Unable to create log', $e->getCode(), $e);
        }

        return $this->getWrappedResponseLog($response, $log);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus(Log $log, $status)
    {
        try {
            $response = $this->httpClient->put($this->baseUrl.'/api/logs/'.$log->getId(), [
                'json' => [
                    'status' => $status,
                ],
            ]);
        } catch (AdapterException $e) {
            throw new ClientException('Unable to update log', $e->getCode(), $e);
        } catch (RequestException $e) {
            throw new ClientException('Unable to update log', $e->getCode(), $e);
        }

        return $this->getWrappedResponseLog($response, $log);
    }

    /**
     * @param ResponseInterface $response
     * @param LogNode           $logNode
     *
     * @return WrappedLog
     *
     * @throws ClientException
     */
    private function getWrappedResponseLog(ResponseInterface $response, LogNode $logNode)
    {
        $json = $response->json();
        if ($json['status'] != 'success') {
            throw new ClientException(sprintf('Response is not successful, got status "%s"', $json['status']));
        }

        $data = $json['data'];

        return new WrappedLog($data['_id'], $logNode, array_key_exists('status', $data) ? $data['status'] : null);
    }
}
