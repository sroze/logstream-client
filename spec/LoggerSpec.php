<?php

namespace spec\LogStream;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use LogStream\Log;
use LogStream\Client;

class LoggerSpec extends ObjectBehavior
{
    function let(Client $client, Log $log)
    {
        $this->beConstructedWith($client, $log);
    }

    function it_append_a_new_log_in_stream(Client $client, Log $log, Log $child)
    {
        $client->create($child, $log)->shouldBeCalled();

        $this->append($child);
    }

    function it_changes_the_current_status(Client $client, Log $log)
    {
        $client->updateStatus($log, 'running')->shouldBeCalled();

        $this->start();
    }
}
