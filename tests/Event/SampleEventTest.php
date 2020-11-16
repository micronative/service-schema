<?php

namespace Micronative\ServiceSchema\Tests\Event;

use PHPUnit\Framework\TestCase;

class SampleEventTest extends TestCase
{
    /** @var string */
    protected $testDir;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\Message::setPayload
     * @covers \Micronative\ServiceSchema\Event\Message::setTime
     * @covers \Micronative\ServiceSchema\Event\Message::setEvent
     * @covers \Micronative\ServiceSchema\Event\Message::setId
     * @covers \Micronative\ServiceSchema\Event\Message::getId
     * @covers \Micronative\ServiceSchema\Event\Message::setStatus
     * @covers \Micronative\ServiceSchema\Event\Message::getStatus
     * @covers \Micronative\ServiceSchema\Event\Message::setDescription
     * @covers \Micronative\ServiceSchema\Event\Message::getDescription
     * @covers \Micronative\ServiceSchema\Event\Message::setSource
     * @covers \Micronative\ServiceSchema\Event\Message::getSource
     * @covers \Micronative\ServiceSchema\Event\Message::setSagaId
     * @covers \Micronative\ServiceSchema\Event\Message::getSagaId
     * @covers \Micronative\ServiceSchema\Event\Message::setAttribute
     * @covers \Micronative\ServiceSchema\Event\Message::getAttribute
     * @covers \Micronative\ServiceSchema\Event\Message::setAttributes
     * @covers \Micronative\ServiceSchema\Event\Message::getAttributes
     * @covers \Micronative\ServiceSchema\Event\Message::toJson
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testToJson()
    {
        $event = new SampleEvent();
        $event->setName("Test.Event.Name");
        $event->setPayload((object) ["name" => "Ken"]);

        $json = $event->toJson();
        $this->assertTrue(is_string($json));
        $this->assertEquals('{"id":null,"name":"Test.Event.Name","payload":{"name":"Ken"}}', $json);

        $event = new SampleEvent();
        $event->setName("Users.afterSaveCommit.Create");
        $event->setPayload(["user" => ["data" => ["name" => "Ken"]], "account" => ["data" => ["name" => "Brighte"]]]);
        $json = $event->toJson();
        $this->assertTrue(is_string($json));
        $this->assertEquals('{"id":null,"name":"Users.afterSaveCommit.Create","payload":{"user":{"data":{"name":"Ken"}},"account":{"data":{"name":"Brighte"}}}}', $json);

        $event = new SampleEvent();
        $event->setId(111);
        $id = $event->getId();
        $this->assertSame($id, '111');
    }
}
