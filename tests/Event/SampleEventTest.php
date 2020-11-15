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
        $event->setTime("SomeTimeString");
        $event->setPayload((object) ["name" => "Ken"]);
        $event->setStatus("new");

        $json = $event->toJson();
        $this->assertTrue(is_string($json));
        $this->assertEquals('{"id":null,"event":"Test.Event.Name","time":"SomeTimeString","payload":{"name":"Ken"},"source":null,"description":null,"status":"new","sagaId":null,"attributes":null}', $json);

        $event = new Message();
        $event->setEvent("Users.afterSaveCommit.Create");
        $event->setTime("20190730123000");
        $event->setPayload(["user" => ["data" => ["name" => "Ken"]], "account" => ["data" => ["name" => "Brighte"]]]);
        $json = $event->toJson();
        $this->assertTrue(is_string($json));
        $this->assertEquals('{"id":null,"event":"Users.afterSaveCommit.Create","time":"20190730123000","payload":{"user":{"data":{"name":"Ken"}},"account":{"data":{"name":"Brighte"}}},"source":null,"description":null,"status":null,"sagaId":null,"attributes":null}', $json);

        $event = new Message();
        $event->setId(111);
        $id = $event->getId();
        $this->assertSame($id, '111');

        $event->setStatus('status');
        $entity = $event->getStatus();
        $this->assertSame($entity, 'status');

        $event->setDescription('description');
        $entity = $event->getDescription();
        $this->assertSame($entity, 'description');

        $event->setSource('source');
        $entity = $event->getSource();
        $this->assertSame($entity, 'source');

        $event->setAttribute('attr', 'val');
        $entity = $event->getAttribute('attr');
        $this->assertSame($entity, 'val');

        $event->setAttributes(['attr', 'attr2']);
        $entity = $event->getAttributes();
        $this->assertSame($entity, ['attr', 'attr2']);

    }
}
