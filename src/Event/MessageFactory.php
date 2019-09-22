<?php

namespace ServiceSchema\Event;

use ServiceSchema\Json\Exception\JsonException;
use ServiceSchema\Json\JsonReader;

class MessageFactory
{

    /**
     * @param string|null $json
     * @return false|\ServiceSchema\Event\Message
     * @throws \ServiceSchema\Json\Exception\JsonException
     */
    public function createMessage(string $json = null)
    {
        if (empty($json)) {
            throw new JsonException(JsonException::MISSING_JSON_CONTENT);
        }

        $object = JsonReader::decode($json);

        if (!$this->validate($object)) {
            throw new JsonException(JsonException::INVALID_JSON_CONTENT . $json);
        }

        return new Message(
            isset($object->event) ? $object->event : null,
            isset($object->time) ? $object->time : null,
            isset($object->payload) ? $object->payload : null,
            isset($object->status) ? $object->status : Message::STATUS_NEW,
            isset($object->queue) ? $object->queue : null,
            isset($object->jwt) ? $object->jwt : null
        );
    }

    /**
     * @param null|\stdClass $object
     * @return bool
     */
    public function validate(\stdClass $object = null)
    {
        if (!is_object($object)) {
            return false;
        }

        return true;
    }
}
