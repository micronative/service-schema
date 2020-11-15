<?php

namespace Micronative\ServiceSchema\Json\Exception;

use Micronative\ServiceSchema\Exceptions\ServiceSchemaException;

class SchemaExporterException extends ServiceSchemaException
{
    const INVALID_SCHEMA_DIR = "Provided path is not a valid directory: ";
}
