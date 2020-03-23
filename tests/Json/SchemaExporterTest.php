<?php

namespace Micronative\ServiceSchema\Tests\Json;

use PHPUnit\Framework\TestCase;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Json\SchemaExporter;
use Micronative\ServiceSchema\Main\Processor;

class SchemaExporterTest extends TestCase
{
    /** @var string */
    protected $testDir;

    /** @var string */
    protected $message;

    /**
     * @var $processor \ServiceSchema\Main\Processor
     */
    protected $processor;

    /**
     * @var $schemaExporter \ServiceSchema\Json\SchemaExporter
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
     * @covers ServiceSchema\Json\SchemaExporter::__construct
     * @covers ServiceSchema\Json\SchemaExporter::export
     * @throws \ServiceSchema\Json\Exception\JsonException
     */
    public function testExport()
    {
        $this->schemaExporter = new SchemaExporter($this->processor);

        $result = $this->schemaExporter->export(schemaExporter::RETURN_JSON);
        $this->assertContains('{"CreateContact":{"type":"object","properties":{"event":{"type":"string","minLength":0,"maxLength":256}', $result);
    }
}