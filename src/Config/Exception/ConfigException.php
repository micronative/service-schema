<?php

namespace BrighteCapital\ServiceSchema\Config\Exception;

use BrighteCapital\ServiceSchema\Exception\ServiceSchemaException;

class ConfigException extends ServiceSchemaException
{
    const MISSING_EVENT_CONFIGS = "Event configs are missing.";
    const MISSING_SERVICE_CONFIGS = "Service configs are missing.";
}
