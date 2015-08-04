<?php

namespace spec\LogStream\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TextSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Blah blah');
    }

    function it_is_a_log()
    {
        $this->shouldImplement('LogStream\LogNode');
    }

    function it_exposes_its_text()
    {
        $this->jsonSerialize()->shouldReturn([
            'type' => 'text',
            'contents' => 'Blah blah'
        ]);
    }
}
