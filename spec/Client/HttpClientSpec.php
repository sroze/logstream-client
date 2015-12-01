<?php

namespace spec\LogStream\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Stream\StreamInterface;
use LogStream\Client\LogNormalizer;
use LogStream\Log;
use PhpSpec\ObjectBehavior;

class HttpClientSpec extends ObjectBehavior
{
    public function let(Client $httpClient, LogNormalizer $logNormalizer)
    {
        $this->beConstructedWith($httpClient, $logNormalizer, 'http://example.com');
    }

    public function it_is_a_client()
    {
        $this->shouldImplement('LogStream\Client');
    }

    public function it_creates_a_log_without_parent(Client $httpClient, LogNormalizer $logNormalizer, Log $log, ResponseInterface $response)
    {
        $logNormalizer->normalize($log)->willReturn(['foo' => 'bar']);
        $response->json()->willReturn([
            'status' => 'success',
            'data' => ['_id' => '1234'],
        ]);

        $httpClient->post('http://example.com/api/logs', ['json' => ['foo' => 'bar']])->willReturn($response);

        $this->create($log);
    }

    public function it_creates_a_log_with_a_parent(Client $httpClient, LogNormalizer $logNormalizer, Log $log, Log $parentLog, ResponseInterface $response)
    {
        $parentLog->getId()->willReturn('1234');
        $logNormalizer->normalize($log)->willReturn(['foo' => 'bar']);
        $response->json()->willReturn([
            'status' => 'success',
            'data' => ['_id' => '1234'],
        ]);
        $httpClient->post('http://example.com/api/logs', ['json' => ['foo' => 'bar', 'parent' => '1234']])->willReturn($response);

        $this->create($log, $parentLog);
    }

    public function it_updates_log_status(Client $httpClient, Log $log, ResponseInterface $response)
    {
        $log->getId()->willReturn('123456');
        $response->json()->willReturn([
            'status' => 'success',
            'data' => ['_id' => '1234'],
        ]);

        $httpClient->put('http://example.com/api/logs/123456', ['json' => ['status' => 'running']])->willReturn($response);
        $this->updateStatus($log, 'running');
    }
}
