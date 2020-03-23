<?php

namespace Micronative\ServiceSchema\Event;

interface MessageInterface
{

    /**
     * @return false|string
     */
    public function toJson();

    /**
     * @return string|null
     */
    public function getId();

    /**
     * @param string|null $id
     * @return \Micronative\ServiceSchema\Event\MessageInterface
     */
    public function setId(string $id = null);

    /**
     * @return string|null
     */
    public function getEvent();

    /**
     * @param string|null $event
     * @return \Micronative\ServiceSchema\Event\MessageInterface
     */
    public function setEvent(string $event = null);

    /**
     * @return string|null
     */
    public function getTime();

    /**
     * @param string|null $time
     * @return \Micronative\ServiceSchema\Event\MessageInterface
     */
    public function setTime(string $time = null);

    /**
     * @return array|\stdClass|null
     */
    public function getPayload();

    /**
     * @param array|\stdClass|null $payload
     * @return \Micronative\ServiceSchema\Event\MessageInterface
     */
    public function setPayload($payload = null);

    /**
     * @return string|null
     */
    public function getStatus();

    /**
     * @param string|null $status
     * @return \Micronative\ServiceSchema\Event\MessageInterface
     */
    public function setStatus(string $status = null);

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @param string|null $description
     * @return \Micronative\ServiceSchema\Event\MessageInterface
     */
    public function setDescription(string $description = null);

    /**
     * @return string|null
     */
    public function getSource();

    /**
     * @param string|null $source
     * @return \Micronative\ServiceSchema\Event\MessageInterface
     */
    public function setSource(string $source = null);

    /**
     * @return string
     */
    public function getSagaId();

    /**
     * @param string|null $sagaId
     * @return \Micronative\ServiceSchema\Event\MessageInterface
     */
    public function setSagaId(string $sagaId = null);

    /**
     * @return array|\stdClass|null
     */
    public function getAttributes();

    /**
     * @param array|\stdClass|null $extra
     * @return \Micronative\ServiceSchema\Event\MessageInterface
     */
    public function setAttributes($extra = null);

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getAttribute(string $key);

    /**
     * @param string $key
     * @param string|array|null $value
     * @return \Micronative\ServiceSchema\Event\MessageInterface
     */
    public function setAttribute(string $key, $value = null);
}
