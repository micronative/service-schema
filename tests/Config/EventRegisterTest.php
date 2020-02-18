<?php

namespace BrighteCapital\ServiceSchema\Tests\Config;

use PHPUnit\Framework\TestCase;
use BrighteCapital\ServiceSchema\Config\EventRegister;

class EventRegisterTest extends TestCase
{
    protected $testDir;

    /** @var EventRegister $eventRegister */
    protected $eventRegister;

    /**
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->eventRegister = new EventRegister([$this->testDir . "/jsons/configs/events.json"]);
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Config\EventRegister::loadEvents
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testLoadEvents()
    {
        $this->eventRegister->loadEvents();
        $events = $this->eventRegister->getEvents();
        $this->assertTrue(is_array($events));
        $this->assertTrue(isset($events["Users.afterSaveCommit.Create"]));
        $this->assertTrue(isset($events["Users.afterSaveCommit.Update"]));
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Config\EventRegister::registerEvent
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testRegisterEvent()
    {
        $this->eventRegister->loadEvents();
        $this->eventRegister->registerEvent("Event.Name", ["SomeServiceClass"]);
        $events = $this->eventRegister->getEvents();
        $this->assertTrue(is_array($events));
        $this->assertTrue(isset($events["Event.Name"]));
        $this->assertEquals(["SomeServiceClass"], $events["Event.Name"]);
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Config\EventRegister::retrieveEvent
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testRetrieveEvent()
    {
        $this->eventRegister->loadEvents();
        $this->eventRegister->registerEvent("Event.Name", ["SomeServiceClass"]);
        $event = $this->eventRegister->retrieveEvent("Event.Name");
        $this->assertTrue(is_array($event));
        $this->assertTrue(isset($event["Event.Name"]));
        $this->assertEquals(["SomeServiceClass"], $event["Event.Name"]);
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Config\EventRegister::getConfigs
     * @covers \BrighteCapital\ServiceSchema\Config\EventRegister::setConfigs
     * @covers \BrighteCapital\ServiceSchema\Config\EventRegister::getEvents
     * @covers \BrighteCapital\ServiceSchema\Config\EventRegister::setEvents
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testGetterAndSetters()
    {
        $this->eventRegister->loadEvents();
        $this->eventRegister->registerEvent("Event.Name", ["SomeServiceClass"]);

        $entity = $this->eventRegister->getConfigs();
        $this->eventRegister->setConfigs($entity);
        $this->assertSame($entity, $this->eventRegister->getConfigs());

        $entity = $this->eventRegister->getEvents();
        $this->eventRegister->setEvents($entity);
        $this->assertSame($entity, $this->eventRegister->getEvents());
    }
}
