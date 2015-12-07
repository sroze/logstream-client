<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use GuzzleHttp\Client;
use LogStream\Client\HttpClient;
use LogStream\Client\LogNormalizer;
use LogStream\Client\WebSocketClient;
use LogStream\Log;
use LogStream\Node\Text;
use LogStream\TreeLoggerFactory;

class ClientContext implements Context, SnippetAcceptingContext
{
    /**
     * @var TreeLoggerFactory
     */
    private $loggerFactory;

    /**
     * @var Log|null
     */
    private $log;

    /**
     * @param string $type
     * @param string $address
     */
    public function __construct($type, $address)
    {
        if ($type == 'http') {
            $client = new HttpClient(
                new Client(),
                new LogNormalizer(),
                $address
            );
        } else if ($type == 'websocket') {
            $client = new WebSocketClient(
                new LogNormalizer(),
                $address
            );
        } else {
            throw new \RuntimeException(sprintf('Client type "%s" is not supported', $type));
        }

        $this->loggerFactory = new TreeLoggerFactory($client);
    }

    /**
     * @When I create an empty container log
     */
    public function iCreateAnEmptyContainerLog()
    {
        $this->log = $this->loggerFactory->create()->getLog();
    }

    /**
     * @Given I have an empty container log
     */
    public function iHaveAnEmptyContainerLog()
    {
        $this->iCreateAnEmptyContainerLog();
    }

    /**
     * @When I create a text log containing :contents under the container log
     */
    public function iCreateATextLogContainingUnderTheContainerLog($contents)
    {
        $this->log = $this->loggerFactory->from($this->log)->append(new Text($contents));
    }

    /**
     * @Given I have a text log
     */
    public function iHaveATextLog()
    {
        $this->iCreateATextLogContainingUnderTheContainerLog('Foo');
    }

    /**
     * @When I update the status of the log with :status
     */
    public function iUpdateTheStatusOfTheLogWith($status)
    {
        $this->log = $this->loggerFactory->from($this->log)->$status();
    }

    /**
     * @Then the log should be successfully created
     * @Then the log should be successfully updated
     */
    public function theLogShouldBeSuccessfullyCreatedOrUpdated()
    {
        if (null === $this->log) {
            throw new \RuntimeException('The found log is null, looks not good at all');
        }
    }
}
