<?php

namespace Micronative\ServiceSchema\Main;

use Micronative\ServiceSchema\Config\EventRegister;
use Micronative\ServiceSchema\Config\ServiceRegister;
use Micronative\ServiceSchema\Event\Message;
use Micronative\ServiceSchema\Event\MessageFactory;
use Micronative\ServiceSchema\Event\MessageInterface;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Main\Exception\ProcessorException;
use Micronative\ServiceSchema\Service\Exception\ServiceException;
use Micronative\ServiceSchema\Service\SagaInterface;
use Micronative\ServiceSchema\Service\ServiceFactory;
use Micronative\ServiceSchema\Service\ServiceInterface;
use Micronative\ServiceSchema\Service\ServiceValidator;
use Psr\Container\ContainerInterface;

class Processor implements ProcessorInterface
{

    /** @var \Micronative\ServiceSchema\Config\EventRegister */
    protected $eventRegister;

    /** @var \Micronative\ServiceSchema\Config\ServiceRegister */
    protected $serviceRegister;

    /** @var \Micronative\ServiceSchema\Event\MessageFactory */
    protected $messageFactory;

    /** @var \Micronative\ServiceSchema\Service\ServiceFactory */
    protected $serviceFactory;

    /** @var \Micronative\ServiceSchema\Service\ServiceValidator */
    protected $serviceValidator;

    /** @var \Psr\Container\ContainerInterface */
    protected $container;

    /**
     * ServiceProvider constructor.
     *
     * @param array|null $eventConfigs
     * @param array|null $serviceConfigs
     * @param string|null $schemaDir
     * @param \Psr\Container\ContainerInterface $container
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function __construct(
        array $eventConfigs = null,
        array $serviceConfigs = null,
        string $schemaDir = null,
        ContainerInterface $container = null)
    {
        $this->eventRegister = new EventRegister($eventConfigs);
        $this->serviceRegister = new ServiceRegister($serviceConfigs);
        $this->serviceFactory = new ServiceFactory();
        $this->messageFactory = new MessageFactory();
        $this->serviceValidator = new ServiceValidator(null, $schemaDir);
        $this->container = $container;
        $this->loadConfigs();
    }

    /**
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    protected function loadConfigs()
    {
        $this->eventRegister->loadEvents();
        $this->serviceRegister->loadServices();
    }

    /**
     * @param string|\Micronative\ServiceSchema\Event\Message $message
     * @param bool $return return first service result
     * @param array|null $filteredEvents
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function process($message = null, array $filteredEvents = null, bool $return = false)
    {
        $message = $this->createMessage($message);
        if (!empty($filteredEvents) && !in_array($message->getEvent(), $filteredEvents)) {
            throw new ProcessorException(ProcessorException::FILTERED_EVENT_ONLY . json_encode($filteredEvents));
        }

        $registeredEvents = $this->eventRegister->retrieveEvent($message->getEvent());
        if (empty($registeredEvents)) {
            throw new ProcessorException(ProcessorException::NO_REGISTER_EVENTS . $message->getEvent());
        }

        foreach ($registeredEvents as $eventName => $services) {
            if (empty($services)) {
                continue;
            }

            foreach ($services as $serviceName) {
                $registerService = $this->serviceRegister->retrieveService($serviceName);
                if (empty($registerService)) {
                    continue;
                }

                $jsonSchema = $registerService[$serviceName][ServiceRegister::INDEX_SCHEMA];
                $callbacks = $registerService[$serviceName][ServiceRegister::INDEX_CALLBACKS];
                $service = $this->serviceFactory->createService($serviceName, $jsonSchema, $this->container);
                if (empty($service)) {
                    continue;
                }

                if ($return === true) {
                    return $this->runService($message, $service, $callbacks, $return);
                }

                $this->runService($message, $service, $callbacks);
            }
        }

        return true;
    }

    /**
     * @param $json
     * @return false|\Micronative\ServiceSchema\Event\Message
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function createMessage($json = null)
    {
        if ($json instanceof Message) {
            return $json;
        }

        $message = $this->messageFactory->createMessage($json);
        if (empty($message)) {
            throw new ProcessorException(ProcessorException::FAILED_TO_CREATE_MESSAGE . $json);
        }

        return $message;
    }

    /**
     * @param \Micronative\ServiceSchema\Event\MessageInterface|null $message
     * @param \Micronative\ServiceSchema\Service\ServiceInterface|null $service
     * @param array $callbacks
     * @param bool $return
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function runService(MessageInterface $message = null, ServiceInterface $service = null, array $callbacks = null, bool $return = false)
    {
        $json = JsonReader::decode($message->toJson());
        $validator = $this->serviceValidator->validate($json, $service);
        if (!$validator->isValid()) {
            throw  new ServiceException(sprintf(ServiceException::INVALIDATED_JSON_STRING, $service->getJsonSchema(), json_encode($validator->getErrors())));
        }

        if (isset($json->payload)) {
            $message->setPayload($json->payload);
        }

        $result = $service->consume($message);
        if ($return === true) {
            return $result;
        }

        if (($result instanceof MessageInterface) && !empty($callbacks)) {
            return $this->runCallbacks($result, $callbacks);
        }

        return $result;
    }

    /**
     * @param \Micronative\ServiceSchema\Event\MessageInterface|null $event
     * @param array|null $callbacks
     * @return bool
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function runCallbacks(MessageInterface $event, array $callbacks = null)
    {
        if (empty($callbacks)) {
            return true;
        }

        foreach ($callbacks as $callback) {
            $service = $this->serviceFactory->createService($callback, null, $this->container);
            if (empty($service)) {
                continue;
            }

            $service->consume($event);
        }

        return true;
    }

    /**
     * @param null $message
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function rollback($message = null)
    {
        $message = $this->createMessage($message);
        $registeredEvents = $this->eventRegister->retrieveEvent($message->getEvent());
        if (empty($registeredEvents)) {
            throw new ProcessorException(ProcessorException::NO_REGISTER_EVENTS . $message->getEvent());
        }

        foreach ($registeredEvents as $eventName => $services) {
            if (empty($services)) {
                continue;
            }

            foreach ($services as $serviceName) {
                $registerService = $this->serviceRegister->retrieveService($serviceName);
                if (empty($registerService)) {
                    continue;
                }

                $jsonSchema = $registerService[$serviceName][ServiceRegister::INDEX_SCHEMA];
                $service = $this->serviceFactory->createService($serviceName, $jsonSchema, $this->container);
                if (empty($service)) {
                    continue;
                }

                if ($service instanceof SagaInterface) {
                    $this->rollbackService($message, $service);
                }
            }
        }

        return true;
    }

    /**
     * @param \Micronative\ServiceSchema\Event\MessageInterface|null $message
     * @param \Micronative\ServiceSchema\Service\SagaInterface|null $service
     * @return bool|\Micronative\ServiceSchema\Event\MessageInterface
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function rollbackService(MessageInterface $message = null, SagaInterface $service = null)
    {
        $json = JsonReader::decode($message->toJson());
        $validator = $this->serviceValidator->validate($json, $service);
        if (!$validator->isValid()) {
            throw  new ServiceException(sprintf(ServiceException::INVALIDATED_JSON_STRING, $service->getJsonSchema(), json_encode($validator->getErrors())));
        }

        if (isset($json->payload)) {
            $message->setPayload($json->payload);
        }

        return $service->rollback($message);
    }

    /**
     * @return \Micronative\ServiceSchema\Config\EventRegister
     */
    public function getEventRegister()
    {
        return $this->eventRegister;
    }

    /**
     * @param \Micronative\ServiceSchema\Config\EventRegister $eventRegister
     * @return \Micronative\ServiceSchema\Main\Processor
     */
    public function setEventRegister(EventRegister $eventRegister = null)
    {
        $this->eventRegister = $eventRegister;

        return $this;
    }

    /**
     * @return \Micronative\ServiceSchema\Config\ServiceRegister
     */
    public function getServiceRegister()
    {
        return $this->serviceRegister;
    }

    /**
     * @param \Micronative\ServiceSchema\Config\ServiceRegister $serviceRegister
     * @return \Micronative\ServiceSchema\Main\Processor
     */
    public function setServiceRegister(ServiceRegister $serviceRegister = null)
    {
        $this->serviceRegister = $serviceRegister;

        return $this;
    }

    /**
     * @return \Micronative\ServiceSchema\Event\MessageFactory
     */
    public function getMessageFactory()
    {
        return $this->messageFactory;
    }

    /**
     * @param \Micronative\ServiceSchema\Event\MessageFactory $messageFactory
     * @return \Micronative\ServiceSchema\Main\Processor
     */
    public function setMessageFactory(MessageFactory $messageFactory = null)
    {
        $this->messageFactory = $messageFactory;

        return $this;
    }

    /**
     * @return \Micronative\ServiceSchema\Service\ServiceFactory
     */
    public function getServiceFactory()
    {
        return $this->serviceFactory;
    }

    /**
     * @param \Micronative\ServiceSchema\Service\ServiceFactory $serviceFactory
     * @return \Micronative\ServiceSchema\Main\Processor
     */
    public function setServiceFactory(ServiceFactory $serviceFactory = null)
    {
        $this->serviceFactory = $serviceFactory;

        return $this;
    }

    /**
     * @return \Micronative\ServiceSchema\Service\ServiceValidator
     */
    public function getServiceValidator()
    {
        return $this->serviceValidator;
    }

    /**
     * @param \Micronative\ServiceSchema\Service\ServiceValidator $serviceValidator
     * @return \Micronative\ServiceSchema\Main\Processor
     */
    public function setServiceValidator(ServiceValidator $serviceValidator = null)
    {
        $this->serviceValidator = $serviceValidator;

        return $this;
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @return Processor
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

}
