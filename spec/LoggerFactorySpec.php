<?php

namespace spec\LogStream;

use LogStream\Client;
use LogStream\Log;
use LogStream\TreeLogger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LoggerFactorySpec extends ObjectBehavior
{
    public function let(Client $client)
    {
        $this->beConstructedWith($client);
    }

    public function it_creates_a_logged_without_parent(Client $client, Log $log)
    {
        $client->create(Argument::any())->willReturn($log);

        $this->create()->shouldBeLike(new TreeLogger(
            $client->getWrappedObject(),
            $log->getWrappedObject()
        ));
    }
}
