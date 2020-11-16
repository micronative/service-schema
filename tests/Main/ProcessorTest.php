<?php

namespace Micronative\ServiceSchema\Tests\Main;

use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Main\Processor;
use Micronative\ServiceSchema\Service\Exception\ServiceException;
use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
{
    protected $testDir;

    /** @var \Micronative\ServiceSchema\Main\Processor */
    protected $processor;

    /**
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->processor = new Processor([$this->testDir . "/assets/configs/events.json"], [$this->testDir . "/assets/configs/services.json"], $this->testDir);
    }

    /**
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testProcess()
    {
        $message = JsonReader::read($this->testDir . "/assets/messages/Users.afterSaveCommit.Create.json");
        $result = $this->processor->process($message);
        $this->assertTrue(is_bool($result));
    }

    /**
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testProcessFailed()
    {
        $message = JsonReader::read($this->testDir . "/assets/messages/Users.afterSaveCommit.Create.Failed.json");
        $this->expectException(ServiceException::class);
        $this->processor->process($message);
    }

    /**
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testRollback()
    {
        $message = JsonReader::read($this->testDir . "/assets/messages/Users.afterSaveCommit.Create.json");
        $result = $this->processor->rollback($message);
        $this->assertTrue(is_bool($result));
    }

    public function testSettersAndGetters()
    {
        $eventRegister = $this->processor->getEventRegister();
        $this->processor->setEventRegister($eventRegister);
        $this->assertSame($eventRegister, $this->processor->getEventRegister());

        $serviceRegister = $this->processor->getServiceRegister();
        $this->processor->setServiceRegister($serviceRegister);
        $this->assertSame($serviceRegister, $this->processor->getServiceRegister());

        $messageFactory = $this->processor->getMessageFactory();
        $this->processor->setMessageFactory($messageFactory);
        $this->assertSame($messageFactory, $this->processor->getMessageFactory());

        $serviceFactory = $this->processor->getServiceFactory();
        $this->processor->setServiceFactory($serviceFactory);
        $this->assertSame($serviceFactory, $this->processor->getServiceFactory());

        $serviceValidator = $this->processor->getServiceValidator();
        $this->processor->setServiceValidator($serviceValidator);
        $this->assertSame($serviceValidator, $this->processor->getServiceValidator());
    }
}
