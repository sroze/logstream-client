<?php

namespace LogStream\Client;

use LogStream\Client;
use LogStream\Log;
use LogStream\LogNode;
use LogStream\WrappedLog;

class WebSocketClient implements Client
{
    /**
     * @var \WebSocket\Client
     */
    private $client;

    /**
     * @var \LogStream\Client\LogNormalizer
     */
    private $normalizer;

    /**
     * @param \LogStream\Client\LogNormalizer $normalizer
     * @param string $url
     */
    public function __construct(Client\LogNormalizer $normalizer, $url)
    {
        $this->client = new \WebSocket\Client($url);
        $this->normalizer = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function create(LogNode $log, Log $parent = null)
    {
        try {
            $this->client->send(json_encode([
                'action' => 'create',
                'body' => $this->normalizer->normalize($log),
            ]));
        } catch (\WebSocket\Exception $e) {
            // Closing connection to allow next attempts to be possibly working
            $this->client->close();

            throw new ClientException('Unable to create log', $e->getCode(), $e);
        }

        return $this->getUpdatedLogFromResponse($log);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus(Log $log, $status)
    {
        try {
            $this->client->send(json_encode([
                'action' => 'update',
                'id' => $log->getId(),
                'body' => $this->normalizer->normalize($log),
            ]));
        } catch (\WebSocket\Exception $e) {
            // Closing connection to allow next attempts to be possibly working
            $this->client->close();

            throw new ClientException('Unable to update log', $e->getCode(), $e);
        }

        return $this->getUpdatedLogFromResponse($log);
    }

    /**
     * @param LogNode $logNode
     *
     * @return WrappedLog
     *
     * @throws \Exception
     */
    private function getUpdatedLogFromResponse(LogNode $logNode)
    {
        $response = $this->receiveResponse();
        if (!in_array($response['status'], [200, 201])) {
            throw new ClientException(sprintf('Status not expected, got %s', $response['status']));
        }

        $body = $response['body'];
        return new WrappedLog($body['_id'], $logNode, array_key_exists('status', $body) ? $body['status'] : null);
    }

    /**
     * Receives a response from the LogStream WebSocket server.
     *
     * @return array
     *
     * @throws \Exception
     */
    private function receiveResponse()
    {
        // Expecting confirmation
        if (null === ($rawResponse = $this->client->receive())) {
            throw new ClientException('Received NULL from WebSocket server after sending log');
        }

        try {
            $response = \GuzzleHttp\json_decode($rawResponse, true);
        } catch (\InvalidArgumentException $e) {
            throw new ClientException('Unable to decode JSON response from WebSocket server');
        }

        if (!array_key_exists('status', $response)) {
            throw new ClientException('Status not found in ACK answer');
        } else if (!array_key_exists('body', $response)) {
            throw new ClientException('No body joined with the ACK answer');
        }

        return $response;
    }
}
