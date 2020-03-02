<?php

namespace BrighteCapital\ServiceSchema\Tests\Event;

use PHPUnit\Framework\TestCase;
use BrighteCapital\ServiceSchema\Event\Event;

class EventTest extends TestCase
{

    protected $testDir;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Event\Event::setPayload
     * @covers \BrighteCapital\ServiceSchema\Event\Event::setTime
     * @covers \BrighteCapital\ServiceSchema\Event\Event::setName
     * @covers \BrighteCapital\ServiceSchema\Event\Event::setId
     * @covers \BrighteCapital\ServiceSchema\Event\Event::getId
     * @covers \BrighteCapital\ServiceSchema\Event\Event::setStatus
     * @covers \BrighteCapital\ServiceSchema\Event\Event::getStatus
     * @covers \BrighteCapital\ServiceSchema\Event\Event::setDescription
     * @covers \BrighteCapital\ServiceSchema\Event\Event::getDescription
     * @covers \BrighteCapital\ServiceSchema\Event\Event::setSource
     * @covers \BrighteCapital\ServiceSchema\Event\Event::getSource
     * @covers \BrighteCapital\ServiceSchema\Event\Event::setSagaId
     * @covers \BrighteCapital\ServiceSchema\Event\Event::getSagaId
     * @covers \BrighteCapital\ServiceSchema\Event\Event::setAttribute
     * @covers \BrighteCapital\ServiceSchema\Event\Event::getAttribute
     * @covers \BrighteCapital\ServiceSchema\Event\Event::setAttributes
     * @covers \BrighteCapital\ServiceSchema\Event\Event::getAttributes
     * @covers \BrighteCapital\ServiceSchema\Event\Event::toJson
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testToJson()
    {
        $event = new Event();
        $event->setName("Test.Event.Name");
        $event->setTime("SomeTimeString");
        $event->setPayload((object) ["name" => "Ken"]);
        $event->setStatus("new");

        $json = $event->toJson();
        $this->assertTrue(is_string($json));
        $this->assertEquals('{"id":null,"name":"Test.Event.Name","time":"SomeTimeString","payload":{"name":"Ken"},"source":null,"description":null,"status":"new","sagaId":null,"attributes":null}', $json);

        $event = new Event();
        $event->setName("Users.afterSaveCommit.Create");
        $event->setTime("20190730123000");
        $event->setPayload(["user" => ["data" => ["name" => "Ken"]], "account" => ["data" => ["name" => "Brighte"]]]);
        $json = $event->toJson();
        $this->assertTrue(is_string($json));
        $this->assertEquals('{"id":null,"name":"Users.afterSaveCommit.Create","time":"20190730123000","payload":{"user":{"data":{"name":"Ken"}},"account":{"data":{"name":"Brighte"}}},"source":null,"description":null,"status":null,"sagaId":null,"attributes":null}', $json);

        $event = new Event();
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
