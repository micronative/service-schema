<?php

namespace Micronative\ServiceSchema\Tests\Event;

use PHPUnit\Framework\TestCase;
use Micronative\ServiceSchema\Event\Message;
use Micronative\ServiceSchema\Event\MessageFactory;

class MessageFactoryTest extends TestCase
{
    /** @var string */
    protected $testDir;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\MessageFactory::createMessage
     * @covers \Micronative\ServiceSchema\Event\MessageFactory::validate
     * @covers \Micronative\ServiceSchema\Event\Message::getEvent
     * @covers \Micronative\ServiceSchema\Event\Message::getTime
     * @covers \Micronative\ServiceSchema\Event\Message::getPayload
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
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
