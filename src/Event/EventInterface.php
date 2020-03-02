<?php

namespace BrighteCapital\ServiceSchema\Event;

interface EventInterface
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
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface
     */
    public function setId(string $id = null);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string|null $name
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface
     */
    public function setName(string $name = null);

    /**
     * @return string|null
     */
    public function getTime();

    /**
     * @param string|null $time
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface
     */
    public function setTime(string $time = null);

    /**
     * @return array|\stdClass|null
     */
    public function getPayload();

    /**
     * @param array|\stdClass|null $payload
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface
     */
    public function setPayload($payload = null);

    /**
     * @return string|null
     */
    public function getStatus();

    /**
     * @param string|null $status
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface
     */
    public function setStatus(string $status = null);

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @param string|null $description
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface
     */
    public function setDescription(string $description = null);

    /**
     * @return string|null
     */
    public function getSource();

    /**
     * @param string|null $source
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface
     */
    public function setSource(string $source = null);

    /**
     * @return string
     */
    public function getSagaId();

    /**
     * @param string|null $sagaId
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface
     */
    public function setSagaId(string $sagaId = null);

    /**
     * @return array|\stdClass|null
     */
    public function getAttributes();

    /**
     * @param array|\stdClass|null $extra
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface
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
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface
     */
    public function setAttribute(string $key, $value = null);
}
