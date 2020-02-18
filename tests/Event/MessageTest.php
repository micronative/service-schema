<?php

namespace BrighteCapital\ServiceSchema\Tests\Event;

use PHPUnit\Framework\TestCase;
use BrighteCapital\ServiceSchema\Event\Message;

class MessageTest extends TestCase
{

    protected $testDir;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Event\Message::setPayload
     * @covers \BrighteCapital\ServiceSchema\Event\Message::setTime
     * @covers \BrighteCapital\ServiceSchema\Event\Message::setEvent
     * @covers \BrighteCapital\ServiceSchema\Event\Message::setId
     * @covers \BrighteCapital\ServiceSchema\Event\Message::getId
     * @covers \BrighteCapital\ServiceSchema\Event\Message::setStatus
     * @covers \BrighteCapital\ServiceSchema\Event\Message::getStatus
     * @covers \BrighteCapital\ServiceSchema\Event\Message::setDescription
     * @covers \BrighteCapital\ServiceSchema\Event\Message::getDescription
     * @covers \BrighteCapital\ServiceSchema\Event\Message::setSource
     * @covers \BrighteCapital\ServiceSchema\Event\Message::getSource
     * @covers \BrighteCapital\ServiceSchema\Event\Message::setSagaId
     * @covers \BrighteCapital\ServiceSchema\Event\Message::getSagaId
     * @covers \BrighteCapital\ServiceSchema\Event\Message::setAttribute
     * @covers \BrighteCapital\ServiceSchema\Event\Message::getAttribute
     * @covers \BrighteCapital\ServiceSchema\Event\Message::setAttributes
     * @covers \BrighteCapital\ServiceSchema\Event\Message::getAttributes
     * @covers \BrighteCapital\ServiceSchema\Event\Message::toJson
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testToJson()
    {
        $event = new Message();
        $event->setEvent("Test.Event.Name");
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

        $event->setSagaId('sagaId');
        $entity = $event->getSagaId();
        $this->assertSame($entity, 'sagaId');

        $event->setAttribute('attr', 'val');
        $entity = $event->getAttribute('attr');
        $this->assertSame($entity, 'val');

        $event->setAttributes(['attr', 'attr2']);
        $entity = $event->getAttributes();
        $this->assertSame($entity, ['attr', 'attr2']);

    }
}
