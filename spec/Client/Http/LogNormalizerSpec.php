<?php

namespace spec\LogStream\Client\Http;

use LogStream\Log;
use PhpSpec\ObjectBehavior;

class LogNormalizerSpec extends ObjectBehavior
{
    public function it_normalize_logs_with_json_serializable_method(Log $log)
    {
        $log->jsonSerialize()->willReturn([
            'foo' => 'bar',
        ]);

        $this->normalize($log)->shouldReturn(['foo' => 'bar']);
    }
}
