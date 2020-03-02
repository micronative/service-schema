<?php

namespace BrighteCapital\ServiceSchema\Tests\Event;

use PHPUnit\Framework\TestCase;
use BrighteCapital\ServiceSchema\Event\Event;
use BrighteCapital\ServiceSchema\Event\EventFactory;

class EventFactoryTest extends TestCase
{
    protected $testDir;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Event\EventFactory::createEvent
     * @covers \BrighteCapital\ServiceSchema\Event\EventFactory::validate
     * @covers \BrighteCapital\ServiceSchema\Event\Event::getName
     * @covers \BrighteCapital\ServiceSchema\Event\Event::getTime
     * @covers \BrighteCapital\ServiceSchema\Event\Event::getPayload
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testCreateEvent()
    {
        $eventFactory = new EventFactory();
        $json = '{"name":"Test.Event.Name","time":"SomeTimeString","payload":{"name":"Ken"}}';
        $event = $eventFactory->createEvent($json);
        $this->assertTrue($event instanceof Event);
        $this->assertEquals("Test.Event.Name", $event->getName());
        $this->assertEquals("SomeTimeString", $event->getTime());
        $this->assertEquals((object)["name" => "Ken"], $event->getPayload());
    }
}
