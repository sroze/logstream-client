<?php

namespace spec\LogStream\Log;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TextSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Blah blah');
    }

    function it_exposes_its_text()
    {
        $this->getContents()->shouldReturn('Blah blah');
    }
}
