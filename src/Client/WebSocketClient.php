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
     * @var Http\LogNormalizer
     */
    private $normalizer;

    /**
     * @param Http\LogNormalizer $normalizer
     * @param string $url
     */
    public function __construct(Client\Http\LogNormalizer $normalizer, $url)
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
            throw $e;
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
            throw $e;
        }

        return $this->getUpdatedLogFromResponse($log);
    }

    /**
     * @param Log $log
     *
     * @return WrappedLog
     *
     * @throws \Exception
     */
    private function getUpdatedLogFromResponse(Log $log)
    {
        $response = $this->receiveResponse();
        if (!in_array($response['status'], [200, 201])) {
            throw new \Exception('Status not expected');
        }

        $body = $response['body'];
        return new WrappedLog($body['_id'], $log, array_key_exists('status', $body) ? $body['status'] : null);
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
            throw new \Exception('Received a NULL ack');
        }

        try {
            $response = \GuzzleHttp\json_decode($rawResponse, true);
        } catch (\InvalidArgumentException $e) {
            throw new \Exception('Unable to decode response');
        }

        if (!array_key_exists('status', $response)) {
            throw new \Exception('Status not found in ACK answer');
        } else if (!array_key_exists('body', $response)) {
            throw new \Exception('No body joined with the ACK answer');
        }

        return $response;
    }
}
