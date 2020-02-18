<?php

namespace BrighteCapital\ServiceSchema\Tests\Service;

use PHPUnit\Framework\TestCase;
use BrighteCapital\ServiceSchema\Service\ServiceFactory;
use BrighteCapital\ServiceSchema\Service\ServiceInterface;

class ServiceFactoryTest extends TestCase
{
    protected $testDir;
    /** @var ServiceFactory */
    protected $serviceFactory;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->serviceFactory = new ServiceFactory();

    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Service\ServiceFactory::createService
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function testCreateService()
    {
        $serviceClass = "\BrighteCapital\ServiceSchema\Tests\Service\Samples\CreateContact";
        $schema = $this->testDir . "/jsons/schemas/CreateContact.json";
        $service = $this->serviceFactory->createService($serviceClass, $schema);
        $this->assertTrue($service instanceof ServiceInterface);
        $this->assertEquals($this->testDir . "/jsons/schemas/CreateContact.json", $service->getJsonSchema());
    }
}
