<?php

namespace Micronative\ServiceSchema\Event;

use Micronative\ServiceSchema\Json\JsonReader;

class Message implements MessageInterface
{

    /** @var string */
    protected $id;

    /** @var string */
    protected $event;

    /** @var string */
    protected $time;

    /** @var array|null|\stdClass */
    protected $payload;

    /** @var string */
    protected $source;

    /** @var string */
    protected $description;

    /** @var string */
    protected $status;

    /** @var string */
    protected $sagaId;

    /** @var array|null */
    protected $attributes;

    /**
     * Message constructor.
     *
     * @param array|null $data
     */
    public function __construct(array $data = null)
    {
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->event = isset($data['event']) ? $data['event'] : null;
        $this->time = isset($data['time']) ? $data['time'] : date("Y-m-d H:i:s");
        $this->payload = isset($data['payload']) ? $data['payload'] : null;
        $this->source = isset($data['source']) ? $data['source'] : null;
        $this->description = isset($data['description']) ? $data['description'] : null;
        $this->status = isset($data['status']) ? $data['status'] : null;
        $this->sagaId = isset($data['sagaId']) ? $data['sagaId'] : null;
        $this->attributes = isset($data['attributes']) ? (array)$data['attributes'] : null;
    }

    /**
     * @return false|string
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function toJson()
    {
        return JsonReader::encode([
            "id" => $this->id,
            "event" => $this->event,
            "time" => $this->time,
            "payload" => $this->payload,
            "source" => $this->source,
            "description" => $this->description,
            "status" => $this->status,
            "sagaId" => $this->sagaId,
            "attributes" => $this->attributes
        ]);
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return \Micronative\ServiceSchema\Event\Message
     */
    public function setId(string $id = null)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param string|null $event
     * @return \Micronative\ServiceSchema\Event\Message
     */
    public function setEvent(string $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param string|null $time
     * @return \Micronative\ServiceSchema\Event\Message
     */
    public function setTime(string $time = null)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return array|\stdClass|null
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array|\stdClass|null $payload
     * @return \Micronative\ServiceSchema\Event\Message
     */
    public function setPayload($payload = null)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return \Micronative\ServiceSchema\Event\Message
     */
    public function setStatus(string $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return \Micronative\ServiceSchema\Event\Message
     */
    public function setDescription(string $description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string|null $source
     * @return \Micronative\ServiceSchema\Event\Message
     */
    public function setSource(string $source = null)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getSagaId()
    {
        return $this->sagaId;
    }

    /**
     * @param string|null $sagaId
     * @return \Micronative\ServiceSchema\Event\Message
     */
    public function setSagaId(string $sagaId = null)
    {
        $this->sagaId = $sagaId;

        return $this;
    }

    /**
     * @return array|\stdClass|null
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array|\stdClass|null $attributes
     * @return \Micronative\ServiceSchema\Event\Message
     */
    public function setAttributes($attributes = null)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getAttribute(string $key)
    {
        if (!isset($this->attributes[$key])) {
            return null;
        }

        return $this->attributes[$key];
    }

    /**
     * @param string $key
     * @param string|array|null $value
     * @return $this
     */
    public function setAttribute(string $key, $value = null)
    {
        $this->attributes[$key] = $value;

        return $this;
    }
}
