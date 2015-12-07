<?php

namespace spec\LogStream\Client;

use FaultTolerance\Waiter;
use LogStream\Client;
use LogStream\Log;
use LogStream\LogNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RetryBufferDecoratorSpec extends ObjectBehavior
{
    function let(Client $client, Waiter $waiter)
    {
        $this->beConstructedWith($client, $waiter);
    }

    function it_calls_the_decorated_client_with_the_correct_arguments(Client $client, LogNode $log, Log $parent)
    {
        $client->create($log, null)->shouldBeCalled();
        $client->create($log, $parent)->shouldBeCalled();

        $this->create($log);
        $this->create($log, $parent);
    }

    function it_retries_when_an_error_occurs(Client $client, LogNode $log)
    {
        $client->create($log, null)->shouldBeCalledTimes(2)
            ->will(function() use($client, $log) {
                $client->create($log, null)->willReturn($log);

                throw new Client\ClientException('Oh, that failed \o/');
            });

        $this->create($log)->shouldReturn($log);
    }
}
