<?php

namespace BrighteCapital\ServiceSchema\Json\Exception;

use BrighteCapital\ServiceSchema\Exception\ServiceSchemaException;

class SchemaExporterException extends ServiceSchemaException
{
    const INVALID_SCHEMA_DIR = "Provided path is not a valid directory: ";
}
