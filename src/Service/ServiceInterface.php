<?php

namespace Micronative\ServiceSchema\Service;

use Micronative\ServiceSchema\Event\AbstractEvent;

interface ServiceInterface
{
    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @return \Micronative\ServiceSchema\Event\AbstractEvent|bool
     */
    public function consume(AbstractEvent $event);

    /**
     * @param string|null $schema
     * @return bool
     */
    public function setJsonSchema(string $schema = null);

    /**
     * @return string
     */
    public function getJsonSchema();

    /**
     * @param string|null $name
     * @return bool
     */
    public function setName(string $name = null);

    /**
     * @return string
     */
    public function getName();

}
