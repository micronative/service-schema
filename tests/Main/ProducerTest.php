<?php

namespace BrighteCapital\ServiceSchema\Main;

use BrighteCapital\ServiceSchema\Event\EventInterface;
use Codeception\AssertThrows;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;
use stdClass;

class ProducerTest extends TestCase
{
    use AssertThrows;
    /** @var Producer */
    protected $producer;
    /** @var Validator */
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new Validator();
        $testDir = dirname(dirname(__FILE__));
        $this->producer = new Producer($testDir . '/jsons/events/', $this->validator);
    }

    public function test(): void
    {
        $this->assertThrowsWithMessage(
            ValidationException::class,
            '[payload.user] The property user is required
[payload.account] The property account is required
[payload] The property test is not defined and the definition does not allow additional properties',
            function () {
                $event = $this->producer->produce('user.created', (object) ['test' => 'test']);
            }
        );
        $this->validator->reset();

        $this->assertThrowsWithMessage(
            ValidationException::class,
            '[payload.user] String value found, but an object is required
[payload.account] String value found, but an object is required',
            function () {
                $event = $this->producer->produce('user.created', (object) ['user' => 'test', 'account' => 'test']);
            }
        );
        $this->validator->reset();

        $event = $this->producer->produce(
            'user.created',
            (object) [
                'user' => (object) ['data' => (object) ['id' => 1]],
                'account' => (object) ['data' => (object) ['id' => 1]]
            ]);
        $this->assertInstanceOf(EventInterface::class, $event);
    }
}
