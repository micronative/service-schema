<?php

namespace BrighteCapital\ServiceSchema\Tests\Event;

use PHPUnit\Framework\TestCase;
use BrighteCapital\ServiceSchema\Event\Message;
use BrighteCapital\ServiceSchema\Event\MessageFactory;

class MessageFactoryTest extends TestCase
{
    protected $testDir;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Event\MessageFactory::createMessage
     * @covers \BrighteCapital\ServiceSchema\Event\MessageFactory::validate
     * @covers \BrighteCapital\ServiceSchema\Event\Message::getEvent
     * @covers \BrighteCapital\ServiceSchema\Event\Message::getTime
     * @covers \BrighteCapital\ServiceSchema\Event\Message::getPayload
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testCreateEvent()
    {
        $messageFactory = new MessageFactory();
        $json = '{"event":"Test.Event.Name","time":"SomeTimeString","payload":{"name":"Ken"}}';
        $message = $messageFactory->createMessage($json);
        $this->assertTrue($message instanceof Message);
        $this->assertEquals("Test.Event.Name", $message->getEvent());
        $this->assertEquals("SomeTimeString", $message->getTime());
        $this->assertEquals((object)["name" => "Ken"], $message->getPayload());
    }
}
