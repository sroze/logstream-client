<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use LogStream\Client\CurlHttp2Client;
use LogStream\Tree\Normalizer\TreeLogNormalizer;
use LogStream\Log;
use LogStream\Node\Normalizer\BaseNormalizer;
use LogStream\Node\Text;
use LogStream\Tree\TreeLoggerFactory;

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
        if ($type == 'http2') {
            $client = new CurlHttp2Client(
                new TreeLogNormalizer(
                    new BaseNormalizer()
                ),
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
        $logger = $this->loggerFactory->from($this->log)->child(new Text($contents));

        $this->log = $logger->getLog();
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
        $this->log = $this->loggerFactory->from($this->log)->updateStatus($status)->getLog();
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

    /**
     * @Then the log should have the status :status
     */
    public function theLogShouldHaveTheStatus($status)
    {
        if ($status != $this->log->getStatus()) {
            throw new \RuntimeException(sprintf(
                'Found status %s instead of %s',
                $this->log->getStatus(),
                $status
            ));
        }
    }
}
