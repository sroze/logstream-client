<?php

namespace spec\LogStream\FaultTolerance;

use LogStream\FaultTolerance\FaultTolerantLogger;
use LogStream\FaultTolerance\FaultTolerantLoggerFactory;
use LogStream\Log;
use LogStream\Logger;
use LogStream\LoggerFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Tolerance\Operation\Callback;
use Tolerance\Operation\Runner\OperationRunner;

class FaultTolerantLoggerFactorySpec extends ObjectBehavior
{
    function let(LoggerFactory $decorated, OperationRunner $operationRunner, LoggerInterface $systemLogger)
    {
        $this->beConstructedWith($decorated, $operationRunner, $systemLogger);
    }

    function it_is_a_logger_factory()
    {
        $this->shouldImplement(LoggerFactory::class);
    }

    function it_creates_a_logger_inside_an_operation(LoggerFactory $decorated, OperationRunner $operationRunner, Logger $child)
    {
        $decorated->create()->willReturn($child);

        $operationRunner->run(Argument::type(Callback::class))->shouldBeCalled()->will(function(array $arguments) {
            return $arguments[0]->call();
        });

        $this->create()->shouldBeAFaultTolerantLoggerWrapping($child);
    }

    function it_wraps_the_logger_when_created_from_id(LoggerFactory $decorated, Logger $child)
    {
        $decorated->fromId('id')->willReturn($child);

        $this->fromId('id')->shouldBeAFaultTolerantLoggerWrapping($child);
    }

    function it_wraps_the_logger_when_created_from_log(LoggerFactory $decorated, Logger $child, Log $log)
    {
        $decorated->from($log)->willReturn($child);

        $this->from($log)->shouldBeAFaultTolerantLoggerWrapping($child);
    }

    public function getMatchers() : array
    {
        return [
            'beAFaultTolerantLoggerWrapping' => function($subject, $wrappedLogger) {
                if (!$subject instanceof FaultTolerantLogger) {
                    return false;
                }

                return $subject->getDecoratedLogger() == $wrappedLogger;
            },
        ];
    }
}
