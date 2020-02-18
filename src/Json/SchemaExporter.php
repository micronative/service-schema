<?php

namespace BrighteCapital\ServiceSchema\Json;

use BrighteCapital\ServiceSchema\Config\ServiceRegister;
use BrighteCapital\ServiceSchema\Main\Processor;

class SchemaExporter
{

    /** @var \BrighteCapital\ServiceSchema\Main\Processor */
    protected $processor;

    const SCHEMA_EXTENSION = 'json';
    const RETURN_JSON = 1;
    const RETURN_ARRAY = 2;

    /**
     * SchemaReader constructor.
     *
     * @param \BrighteCapital\ServiceSchema\Main\Processor|null $processor
     */
    public function __construct(Processor $processor = null)
    {
        $this->processor = $processor;
    }

    /**
     * @param int $returnType
     * @return array|string
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function export(int $returnType = self::RETURN_ARRAY)
    {
        $files = [];
        $services = $this->processor->getServiceRegister()->getServices();
        foreach ($services as $service) {
            $files[$service[ServiceRegister::INDEX_SCHEMA]] = $this->processor->getServiceValidator()->getSchemaDir() . $service[ServiceRegister::INDEX_SCHEMA];
        }

        $schemas = [];
        foreach ($files as $file) {
            $schemas[basename($file, '.' . self::SCHEMA_EXTENSION)] = JsonReader::decode(JsonReader::read($file), true);
        }

        switch ($returnType) {
            case self::RETURN_JSON:
                return JsonReader::encode($schemas);
                break;
            case self::RETURN_ARRAY:
            default:
                return $schemas;
                break;
        }
    }
}
