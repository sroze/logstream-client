<?php

namespace spec\LogStream;

use LogStream\Client;
use LogStream\Log;
use LogStream\Logger;
use LogStream\Node\Container;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LoggerFactorySpec extends ObjectBehavior
{
    function let(Client $client)
    {
        $this->beConstructedWith($client);
    }

    function it_creates_a_logged_without_parent(Client $client, Log $log)
    {
        $client->create(Argument::any())->willReturn($log);

        $this->create()->shouldBeLike(new Logger(
            $client->getWrappedObject(),
            $log->getWrappedObject()
        ));
    }
}
