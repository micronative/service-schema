<?php

namespace BrighteCapital\ServiceSchema\Tests\Json;

use PHPUnit\Framework\TestCase;
use BrighteCapital\ServiceSchema\Json\Exception\JsonException;
use BrighteCapital\ServiceSchema\Json\JsonReader;

class JsonReaderTest extends TestCase
{
    /** @var string */
    protected $testDir;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Json\JsonReader::read
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testReadFailed()
    {
        $this->expectException(JsonException::class);
        JsonReader::read(null);

        $this->expectException(JsonException::class);
        JsonReader::read("someinvalidfile");
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Json\JsonReader::decode
     * @covers \BrighteCapital\ServiceSchema\Json\JsonReader::read
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testDecodeFailed()
    {
        $this->expectException(JsonException::class);
        JsonReader::decode(null);
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Json\JsonReader::read
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testRead()
    {
        $file = $this->testDir . "/jsons/messages/Users.afterSaveCommit.Create.json";
        $json = JsonReader::read($file);
        $this->assertTrue(is_string($json));
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Json\JsonReader::read
     * @covers \BrighteCapital\ServiceSchema\Json\JsonReader::decode
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testDecode()
    {
        $file = $this->testDir . "/jsons/messages/Users.afterSaveCommit.Create.json";
        $json = JsonReader::read($file);
        $object = JsonReader::decode($json);

        $this->assertTrue(is_object($object));
        $this->assertEquals("Users.afterSaveCommit.Create", $object->name);
        $this->assertEquals("20190726032212", $object->time);
        $this->assertTrue(isset($object->payload));
        $this->assertEquals("Ken", $object->payload->user->data->name);
        $this->assertTrue(isset($object->payload->account->data->name));
        $this->assertEquals("Brighte", $object->payload->account->data->name);
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Json\JsonReader::encode
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testEncodeFailed()
    {
        $this->expectException(JsonException::class);
        JsonReader::encode(null);
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Json\JsonReader::encode
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testEncode()
    {
        $array = ["name" => "Ken"];
        $json = JsonReader::encode($array);
        $this->assertTrue(is_string($json));
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Json\JsonReader::save
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testSave()
    {
        $file = $this->testDir . "/jsons/JsonReader/testSave.json";
        $array = ["name" => "Ken"];
        $json = JsonReader::encode($array);
        JsonReader::save($file, $json);
        $contents = file_get_contents($file);
        $this->assertSame('{"name":"Ken"}', $contents);
    }
}
