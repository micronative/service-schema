<?php

namespace ServiceSchema\Main;

use ServiceSchema\Event\Message;
use ServiceSchema\Event\MessageInterface;
use ServiceSchema\Json\JsonReader;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

class Producer
{
    /** @var string */
    protected $eventSchemasDir;
    /** @var \JsonSchema\Validator */
    protected $validator;

    public function __construct(
        string $eventSchemasDir,
        Validator $validator
    ) {
        $this->eventSchemasDir = $eventSchemasDir;
        $this->validator = $validator;
    }

    /**
     * @param string $name name of the event
     * @param object $payload payload for event
     * @return MessageInterface the event
     */
    public function produce(string $name, $payload): MessageInterface
    {
        $schema = JsonReader::decode(JsonReader::read($this->eventSchemasDir . $name . '.json'));
        $event = (new Message())->setEvent($name)->setPayload($payload);
        $this->validator->validate($event, $schema, Constraint::CHECK_MODE_APPLY_DEFAULTS);
        if ($this->validator->isValid())
            return $event;
        $errors = implode("\n", array_map(function (array $error) {
            return sprintf("[%s] %s", $error['property'], $error['message']);
        }, $this->validator->getErrors()));
        throw new ValidationException($errors);
    }
}
