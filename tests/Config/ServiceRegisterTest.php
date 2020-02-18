<?php

namespace BrighteCapital\ServiceSchema\Tests\Config;

use PHPUnit\Framework\TestCase;
use BrighteCapital\ServiceSchema\Config\ServiceRegister;

class ServiceRegisterTest extends TestCase
{
    protected $testDir;

    /** @var ServiceRegister */
    protected $serviceRegister;

    /**
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->serviceRegister = new ServiceRegister([$this->testDir . "/jsons/configs//services.json"]);
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Config\ServiceRegister::loadServices
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testLoadServices()
    {
        $this->serviceRegister->loadServices();
        $services = $this->serviceRegister->getServices();
        $this->assertTrue(is_array($services));
        $this->assertTrue(isset($services["BrighteCapital\ServiceSchema\Tests\Service\Samples\CreateContact"]));
        $this->assertTrue(isset($services["BrighteCapital\ServiceSchema\Tests\Service\Samples\UpdateContact"]));
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Config\ServiceRegister::registerService
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testRegisterService()
    {
        $this->serviceRegister->loadServices();
        $this->serviceRegister->registerService("Service.Name", "SomeServiceSchema");
        $services = $this->serviceRegister->getServices();
        $this->assertTrue(is_array($services));
        $this->assertTrue(isset($services["Service.Name"]));
        $this->assertEquals("SomeServiceSchema", $services["Service.Name"]['schema']);
    }

    /**
     * @covers \BrighteCapital\ServiceSchema\Config\ServiceRegister::retrieveService
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function testRetrieveEvent()
    {
        $this->serviceRegister->loadServices();
        $this->serviceRegister->registerService("Service.Name", "SomeServiceSchema");
        $service = $this->serviceRegister->retrieveService("Service.Name");
        $this->assertTrue(is_array($service));
        $this->assertTrue(isset($service["Service.Name"]));
        $this->assertEquals("SomeServiceSchema", $service["Service.Name"]['schema']);
    }
}
