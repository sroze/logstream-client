<?php

namespace spec\LogStream\Node;

use PhpSpec\ObjectBehavior;

class TextSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('Blah blah');
    }

    public function it_is_a_log()
    {
        $this->shouldImplement('LogStream\LogNode');
    }

    public function it_exposes_its_text()
    {
        $this->jsonSerialize()->shouldReturn([
            'type' => 'text',
            'contents' => 'Blah blah',
        ]);
    }
}
