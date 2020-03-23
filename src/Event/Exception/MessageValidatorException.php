<?php

namespace Micronative\ServiceSchema\Event\Exception;

use Micronative\ServiceSchema\Exception\ServiceSchemaException;

class MessageValidatorException extends ServiceSchemaException
{
    const INVALID_JSON_STRING = "Message->toJson is invalid Json string.";
    const MISSING_EVENT_SCHEMA = "Event schema is missing.";
    const INVALIDATED_EVENT_MESSAGE = "Event Message is not validated by event schema. Error: ";
}
