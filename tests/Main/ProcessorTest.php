<?php

namespace BrighteCapital\ServiceSchema\Tests\Main;

use PHPUnit\Framework\TestCase;
use BrighteCapital\ServiceSchema\Json\JsonReader;
use BrighteCapital\ServiceSchema\Main\Processor;
use BrighteCapital\ServiceSchema\Service\Exception\ServiceException;
use BrighteCapital\ServiceSchema\Service\SagaInterface;

class ProcessorTest extends TestCase
{
    protected $testDir;

    /** @var Processor */
    protected $processor;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->processor = new Processor([$this->testDir . "/jsons/configs/events.json"], [$this->testDir . "/jsons/configs/services.json"], $this->testDir);
    }

    /**
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Main\Exception\ProcessorException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function testProcess()
    {
        $event = JsonReader::read($this->testDir . "/jsons/messages/Users.afterSaveCommit.Create.json");
        $result = $this->processor->process($event);
        $this->assertTrue(is_bool($result));
    }

    /**
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Main\Exception\ProcessorException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function testProcessFailed()
    {
        $event = JsonReader::read($this->testDir . "/jsons/messages/Users.afterSaveCommit.Create.Failed.json");
        $this->expectException(ServiceException::class);
        $this->processor->process($event);
    }

    /**
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Main\Exception\ProcessorException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function testRollback()
    {
        $event = JsonReader::read($this->testDir . "/jsons/messages/Users.afterSaveCommit.Create.json");
        $result = $this->processor->rollback($event);
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

        $eventFactory = $this->processor->getEventFactory();
        $this->processor->setEventFactory($eventFactory);
        $this->assertSame($eventFactory, $this->processor->getEventFactory());

        $serviceFactory = $this->processor->getServiceFactory();
        $this->processor->setServiceFactory($serviceFactory);
        $this->assertSame($serviceFactory, $this->processor->getServiceFactory());

        $serviceValidator = $this->processor->getServiceValidator();
        $this->processor->setServiceValidator($serviceValidator);
        $this->assertSame($serviceValidator, $this->processor->getServiceValidator());
    }
}
