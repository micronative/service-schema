<?php

namespace BrighteCapital\ServiceSchema\Tests\Service;

use PHPUnit\Framework\TestCase;
use BrighteCapital\ServiceSchema\Json\JsonReader;
use BrighteCapital\ServiceSchema\Service\ServiceValidator;
use BrighteCapital\ServiceSchema\Tests\Service\Samples\CreateContact;

class ServiceValidatorTest extends TestCase
{
    protected $testDir;
    /** @var ServiceValidator */
    protected $serviceValidator;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->serviceValidator = new ServiceValidator();
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Service\ServiceValidator::validate
     * @covers \BrighteCapital\ServiceSchema\Service\ServiceValidator::getValidator
     * @covers \BrighteCapital\ServiceSchema\Service\ServiceValidator::setValidator
     * @covers \BrighteCapital\ServiceSchema\Service\ServiceValidator::getSchemaDir
     * @covers \BrighteCapital\ServiceSchema\Service\ServiceValidator::setSchemaDir
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function testValidate()
    {
        $file = $this->testDir . "/jsons/messages/Users.afterSaveCommit.Create.json";
        $jsonObject = JsonReader::decode(JsonReader::read($file));
        $service = new CreateContact();
        $service->setJsonSchema($this->testDir . "/jsons/schemas/CreateContact.json");
        $validator = $this->serviceValidator->validate($jsonObject, $service);
        $this->assertTrue($validator->isValid());

        $validator = $this->serviceValidator->getValidator();
        $this->serviceValidator->setValidator($validator);
        $this->assertSame($validator, $this->serviceValidator->getValidator());

        $this->serviceValidator->setSchemaDir($this->testDir);
        $schemaDir = $this->serviceValidator->getSchemaDir();
        $this->assertSame($schemaDir, $this->serviceValidator->getSchemaDir());
    }
}
