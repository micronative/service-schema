<?php

namespace BrighteCapital\ServiceSchema\Tests\Json;

use PHPUnit\Framework\TestCase;
use BrighteCapital\ServiceSchema\Json\JsonReader;
use BrighteCapital\ServiceSchema\Json\SchemaExporter;
use BrighteCapital\ServiceSchema\Main\Processor;

class SchemaExporterTest extends TestCase
{
    /** @var string */
    protected $testDir;

    /** @var string */
    protected $message;

    /**
     * @var $processor \BrighteCapital\ServiceSchema\Main\Processor
     */
    protected $processor;

    /**
     * @var $schemaExporter \BrighteCapital\ServiceSchema\Json\SchemaExporter
     */
    protected $schemaExporter;


    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->processor = new Processor([$this->testDir . "/jsons/configs/events.json"], [$this->testDir . "/jsons/configs/services.json"], $this->testDir);
        $this->message = JsonReader::read($this->testDir . "/jsons/messages/Users.afterSaveCommit.Create.json");
        $this->schema = JsonReader::read($this->testDir . "/jsons/schemas/CreateContact.json");

    }

    /**
     * @covers BrighteCapital\ServiceSchema\Json\SchemaExporter::__construct
     * @covers BrighteCapital\ServiceSchema\Json\SchemaExporter::export
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testExport()
    {
        $this->schemaExporter = new SchemaExporter($this->processor);

        $result = $this->schemaExporter->export(schemaExporter::RETURN_JSON);
        $this->assertContains('{"CreateContact":{"type":"object","properties":{"event":{"type":"string","minLength":0,"maxLength":256}', $result);
    }
}