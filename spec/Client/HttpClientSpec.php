<?php

namespace spec\LogStream\Client;

use GuzzleHttp\Client;
use LogStream\Client\Http\LogNormalizer;
use LogStream\Log;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HttpClientSpec extends ObjectBehavior
{
    function let(Client $httpClient, LogNormalizer $logNormalizer)
    {
        $this->beConstructedWith($httpClient, $logNormalizer, 'http://example.com');
    }

    function it_is_a_client()
    {
        $this->shouldImplement('LogStream\Client');
    }

    function it_creates_a_log_without_parent(Client $httpClient, LogNormalizer $logNormalizer, Log $log)
    {
        $logNormalizer->normalize($log)->willReturn(['foo' => 'bar']);
        $httpClient->post('http://example.com/api/logs', ['json' => ['foo' => 'bar']])->shouldBeCalled();

        $this->create($log);
    }

    function it_creates_a_log_with_a_parent(Client $httpClient, LogNormalizer $logNormalizer, Log $log, Log $parentLog)
    {
        $parentLog->getId()->willReturn('1234');
        $logNormalizer->normalize($log)->willReturn(['foo' => 'bar']);
        $httpClient->post('http://example.com/api/logs', ['json' => ['foo' => 'bar', 'parent' => '1234']])->shouldBeCalled();

        $this->create($log, $parentLog);
    }

    function it_updates_log_status(Client $httpClient, Log $log)
    {
        $log->getId()->willReturn('123456');
        $httpClient->put('http://example.com/api/logs/123456', ['json' => ['status' => 'running']])->shouldBeCalled();
        $this->updateStatus($log, 'running');
    }
}
