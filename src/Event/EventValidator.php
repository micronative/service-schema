<?php

namespace Micronative\ServiceSchema\Event;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use Micronative\ServiceSchema\Event\Exception\EventValidatorException;
use Micronative\ServiceSchema\Json\JsonReader;

class EventValidator
{
    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent|null $event
     * @param string|null $eventSchema
     * @return bool
     * @throws \Micronative\ServiceSchema\Event\Exception\EventValidatorException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public static function validate(AbstractEvent $event = null, ?string $eventSchema = null)
    {
        $jsonObject = JsonReader::decode($event->toJson());
        if (empty($jsonObject)) {
            throw new EventValidatorException(EventValidatorException::INVALID_JSON_STRING);
        }

        if (empty($eventSchema)) {
            throw new EventValidatorException(EventValidatorException::MISSING_EVENT_SCHEMA);
        }

        $schema = JsonReader::decode(JsonReader::read($eventSchema));
        $validator = new Validator();
        $validator->validate($jsonObject, $schema, Constraint::CHECK_MODE_APPLY_DEFAULTS);

        if (!$validator->isValid()) {
            throw new EventValidatorException(EventValidatorException::INVALIDATED_EVENT_MESSAGE . json_encode($validator->getErrors()));
        }

        return $validator->isValid();
    }

}
