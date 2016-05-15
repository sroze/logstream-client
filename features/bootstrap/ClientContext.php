<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use LogStream\Client;
use LogStream\Client\CurlHttp2Client;
use LogStream\LoggerFactory;
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
     * @var Client
     */
    private $client;

    /**
     * @var Log|null
     */
    private $log;

    /**
     * @param LoggerFactory|string $loggerFactoryOrAddress
     * @param Client $client
     */
    public function __construct($loggerFactoryOrAddress, Client $client = null)
    {
        if ($client instanceof Client) {
            $this->client = $client;
        } else {
            $this->client = new CurlHttp2Client(
                new TreeLogNormalizer(
                    new BaseNormalizer()
                ),
                $loggerFactoryOrAddress,
                false
            );
        }

        if ($loggerFactoryOrAddress instanceof LoggerFactory) {
            $this->loggerFactory = $loggerFactoryOrAddress;
        } else if (is_string($loggerFactoryOrAddress)) {
            $this->loggerFactory = new TreeLoggerFactory($this->client);
        } else {
            throw new \RuntimeException(sprintf('Should be either an address or a log factory'));
        }
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
     * @When I archive the log
     */
    public function iArchiveTheLog()
    {
        $this->log = $this->client->archive($this->log);
    }

    /**
     * @Then the log should be archived
     */
    public function theLogShouldBeArchived()
    {
        $raw = $this->log->getNode()->jsonSerialize();

        if (!array_key_exists('archived', $raw)) {
            throw new \RuntimeException('The log is not archived');
        }
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
