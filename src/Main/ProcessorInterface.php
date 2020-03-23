<?php

namespace Micronative\ServiceSchema\Main;

use Micronative\ServiceSchema\Event\MessageInterface;
use Micronative\ServiceSchema\Service\ServiceInterface;

interface ProcessorInterface
{

    /**
     * @param string|\Micronative\ServiceSchema\Event\Message $json
     * @param bool $return return first service result
     * @param array|null $filteredEvents
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function process($json = null, array $filteredEvents = null, bool $return = false);

    /**
     * @param string|\Micronative\ServiceSchema\Event\Message $json
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function rollback($json = null);

    /**
     * @param \Micronative\ServiceSchema\Event\MessageInterface|null $message
     * @param \Micronative\ServiceSchema\Service\ServiceInterface|null $service
     * @param array|null $callbacks
     * @return mixed
     */
    public function runService(MessageInterface $message = null, ServiceInterface $service = null, array $callbacks = null);

    /**
     * @param \Micronative\ServiceSchema\Event\MessageInterface $event
     * @param array|null $callbacks
     * @return mixed
     */
    public function runCallbacks(MessageInterface $event, array $callbacks = null);
}
