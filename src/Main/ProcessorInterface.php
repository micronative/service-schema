<?php

namespace BrighteCapital\ServiceSchema\Main;

use BrighteCapital\ServiceSchema\Event\MessageInterface;
use BrighteCapital\ServiceSchema\Service\ServiceInterface;

interface ProcessorInterface
{

    /**
     * @param string|\BrighteCapital\ServiceSchema\Event\Message $json
     * @param bool $return return first service result
     * @param array|null $filteredEvents
     * @return bool
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     * @throws \BrighteCapital\ServiceSchema\Main\Exception\ProcessorException
     */
    public function process($json = null, array $filteredEvents = null, bool $return = false);

    /**
     * @param string|\BrighteCapital\ServiceSchema\Event\Message $json
     * @return bool
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Main\Exception\ProcessorException
     */
    public function rollback($json = null);

    /**
     * @param \BrighteCapital\ServiceSchema\Event\MessageInterface|null $message
     * @param \BrighteCapital\ServiceSchema\Service\ServiceInterface|null $service
     * @param array|null $callbacks
     * @return mixed
     */
    public function runService(MessageInterface $message = null, ServiceInterface $service = null, array $callbacks = null);

    /**
     * @param \BrighteCapital\ServiceSchema\Event\MessageInterface $event
     * @param array|null $callbacks
     * @return mixed
     */
    public function runCallbacks(MessageInterface $event, array $callbacks = null);
}
