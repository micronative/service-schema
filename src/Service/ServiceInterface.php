<?php

namespace Micronative\ServiceSchema\Service;

use Micronative\ServiceSchema\Event\MessageInterface;
use Psr\Container\ContainerInterface;

interface ServiceInterface
{
    /**
     * @param \Micronative\ServiceSchema\Event\MessageInterface $message
     * @return \Micronative\ServiceSchema\Event\MessageInterface|bool
     */
    public function consume(MessageInterface $message = null);

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

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer();

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @return Service
     */
    public function setContainer(ContainerInterface $container = null);
}
