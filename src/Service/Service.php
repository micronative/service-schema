<?php


namespace Micronative\ServiceSchema\Service;


class Service
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $jsonSchema;

    /**
     * @return string
     */
    public function getJsonSchema()
    {
        return $this->jsonSchema;
    }

    /**
     * @param string $schema
     * @return Service
     */
    public function setJsonSchema(string $schema = null)
    {
        $this->jsonSchema = $schema;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Service
     */
    public function setName(string $name = null)
    {
        $this->name = $name;

        return $this;
    }

}
