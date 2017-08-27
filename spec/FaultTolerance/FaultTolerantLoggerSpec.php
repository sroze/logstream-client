<?php

namespace spec\LogStream\FaultTolerance;

use LogStream\Client\ClientException;
use LogStream\FaultTolerance\FaultTolerantLogger;
use LogStream\Log;
use LogStream\Logger;
use LogStream\Node\Text;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Tolerance\Operation\Callback;
use Tolerance\Operation\Runner\OperationRunner;

class FaultTolerantLoggerSpec extends ObjectBehavior
{
    function let(Logger $decorated, OperationRunner $operationRunner, LoggerInterface $systemLogger)
    {
        $this->beConstructedWith($decorated, $operationRunner, $systemLogger);
    }
    function it_is_a_logger()
    {
        $this->shouldImplement(Logger::class);
    }

    function it_creates_children_in_an_operation(OperationRunner $operationRunner, Logger $decorated, Logger $child)
    {
        $node = new Text('Foo');
        $decorated->child($node)->willReturn($child);

        $operationRunner->run(Argument::type(Callback::class))->shouldBeCalled()->will(function(array $arguments) {
            return $arguments[0]->call();
        });

        $this->child($node)->shouldBeAFaultTolerantLoggerWrapping($child);
    }

    function it_updates_a_log_in_an_operation(OperationRunner $operationRunner, Logger $decorated, Logger $child)
    {
        $node = new Text('Foo updated');
        $decorated->update($node)->willReturn($child);

        $operationRunner->run(Argument::type(Callback::class))->shouldBeCalled()->will(function(array $arguments) {
            return $arguments[0]->call();
        });

        $this->update($node)->shouldBeAFaultTolerantLoggerWrapping($child);
    }

    function it_updates_the_status_of_a_log_in_an_operation(OperationRunner $operationRunner, Logger $decorated, Logger $child)
    {
        $decorated->updateStatus(Log::SUCCESS)->willReturn($child);

        $operationRunner->run(Argument::type(Callback::class))->shouldBeCalled()->will(function(array $arguments) {
            return $arguments[0]->call();
        });

        $this->updateStatus(Log::SUCCESS)->shouldBeAFaultTolerantLoggerWrapping($child);
    }

    function it_throws_an_exception_if_the_child_creation_fails(OperationRunner $operationRunner, Logger $decorated, Logger $child)
    {
        $node = new Text('Foo');
        $decorated->child($node)->willThrow(new ClientException('Found status 0'));

        $operationRunner->run(Argument::type(Callback::class))->shouldBeCalled()->will(function(array $arguments) {
            return $arguments[0]->call();
        });

        $this->shouldThrow(ClientException::class)->duringChild($node);
    }

    function it_catches_and_log_if_update_failed(OperationRunner $operationRunner, Logger $decorated, LoggerInterface $systemLogger)
    {
        $node = new Text('Foo updated');
        $exception = new ClientException('Found status 0');
        $decorated->update($node)->willThrow($exception);

        $operationRunner->run(Argument::type(Callback::class))->shouldBeCalled()->will(function(array $arguments) {
            return $arguments[0]->call();
        });

        $systemLogger->warning('Cannot update LogStream log', [
            'exception' => $exception
        ])->shouldBeCalled();

        $this->update($node)->shouldReturn($this);
    }

    function it_catches_and_log_if_status_update_failed(OperationRunner $operationRunner, Logger $decorated, LoggerInterface $systemLogger)
    {
        $exception = new ClientException('Found status 0');
        $decorated->updateStatus(Log::FAILURE)->willThrow($exception);

        $operationRunner->run(Argument::type(Callback::class))->shouldBeCalled()->will(function(array $arguments) {
            return $arguments[0]->call();
        });

        $systemLogger->warning('Cannot update LogStream log', [
            'exception' => $exception
        ])->shouldBeCalled();

        $this->updateStatus(Log::FAILURE)->shouldReturn($this);
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
