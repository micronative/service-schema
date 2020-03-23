<?php

namespace Micronative\ServiceSchema\Event;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use Micronative\ServiceSchema\Event\Exception\MessageValidatorException;
use Micronative\ServiceSchema\Json\JsonReader;

class MessageValidator
{
    /**
     * @param \Micronative\ServiceSchema\Event\MessageInterface|null $message
     * @param string|null $eventSchema
     * @return bool
     * @throws \Micronative\ServiceSchema\Event\Exception\MessageValidatorException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public static function validate(MessageInterface $message = null, ?string $eventSchema = null)
    {
        $jsonObject = JsonReader::decode($message->toJson());
        if (empty($jsonObject)) {
            throw new MessageValidatorException(MessageValidatorException::INVALID_JSON_STRING);
        }

        if (empty($eventSchema)) {
            throw new MessageValidatorException(MessageValidatorException::MISSING_EVENT_SCHEMA);
        }

        $schema = JsonReader::decode(JsonReader::read($eventSchema));
        $validator = new Validator();
        $validator->validate($jsonObject, $schema, Constraint::CHECK_MODE_APPLY_DEFAULTS);

        if (!$validator->isValid()) {
            throw new MessageValidatorException(MessageValidatorException::INVALIDATED_EVENT_MESSAGE . json_encode($validator->getErrors()));
        }

        return $validator->isValid();
    }

}
