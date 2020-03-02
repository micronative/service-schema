<?php

namespace BrighteCapital\ServiceSchema\Service;

use BrighteCapital\ServiceSchema\Event\EventInterface;

interface ServiceInterface
{
    /**
     * @param \BrighteCapital\ServiceSchema\Event\EventInterface $event
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface|bool
     */
    public function consume(EventInterface $event = null);

    /**
     * @param string $schema
     * @return bool
     */
    public function setJsonSchema(string $schema = null);

    /**
     * @return string
     */
    public function getJsonSchema();

    /**
     * @param string $name
     * @return bool
     */
    public function setName(string $name = null);

    /**
     * @return string
     */
    public function getName();
}
