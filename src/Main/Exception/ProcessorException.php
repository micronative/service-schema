<?php


namespace Micronative\ServiceSchema\Main\Exception;

use Micronative\ServiceSchema\Exceptions\ServiceSchemaException;

class ProcessorException extends ServiceSchemaException
{
    const FAILED_TO_CREATE_MESSAGE = "Failed to create message from json string: ";
    const NO_REGISTER_EVENTS = "No registered events for: ";
    const FILTERED_EVENT_ONLY = "Only filtered events are allowed to process: ";
}
