<?php

namespace BrighteCapital\ServiceSchema\Main;

use BrighteCapital\ServiceSchema\Config\EventRegister;
use BrighteCapital\ServiceSchema\Config\ServiceRegister;
use BrighteCapital\ServiceSchema\Event\Event;
use BrighteCapital\ServiceSchema\Event\EventFactory;
use BrighteCapital\ServiceSchema\Event\EventInterface;
use BrighteCapital\ServiceSchema\Json\JsonReader;
use BrighteCapital\ServiceSchema\Main\Exception\ProcessorException;
use BrighteCapital\ServiceSchema\Service\Exception\ServiceException;
use BrighteCapital\ServiceSchema\Service\SagaInterface;
use BrighteCapital\ServiceSchema\Service\ServiceFactory;
use BrighteCapital\ServiceSchema\Service\ServiceInterface;
use BrighteCapital\ServiceSchema\Service\ServiceValidator;

class Processor implements ProcessorInterface
{

    /** @var \BrighteCapital\ServiceSchema\Config\EventRegister */
    protected $eventRegister;

    /** @var \BrighteCapital\ServiceSchema\Config\ServiceRegister */
    protected $serviceRegister;

    /** @var \BrighteCapital\ServiceSchema\Event\EventFactory */
    protected $eventFactory;

    /** @var \BrighteCapital\ServiceSchema\Service\ServiceFactory */
    protected $serviceFactory;

    /** @var \BrighteCapital\ServiceSchema\Service\ServiceValidator */
    protected $serviceValidator;

    /**
     * ServiceProvider constructor.
     *
     * @param array|null $eventConfigs
     * @param array|null $serviceConfigs
     * @param string|null $schemaDir
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    public function __construct(array $eventConfigs = null, array $serviceConfigs = null, string $schemaDir = null)
    {
        $this->eventRegister = new EventRegister($eventConfigs);
        $this->serviceRegister = new ServiceRegister($serviceConfigs);
        $this->serviceFactory = new ServiceFactory();
        $this->eventFactory = new EventFactory();
        $this->serviceValidator = new ServiceValidator(null, $schemaDir);
        $this->loadConfigs();
    }

    /**
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     */
    protected function loadConfigs()
    {
        $this->eventRegister->loadEvents();
        $this->serviceRegister->loadServices();
    }

    /**
     * @param string|\BrighteCapital\ServiceSchema\Event\Event $event
     * @param bool $return return first service result
     * @param array|null $filteredEvents
     * @return bool
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     * @throws \BrighteCapital\ServiceSchema\Main\Exception\ProcessorException
     */
    public function process($event = null, array $filteredEvents = null, bool $return = false)
    {
        $event = $this->createEvent($event);
        if (!empty($filteredEvents) && !in_array($event->getName(), $filteredEvents)) {
            throw new ProcessorException(ProcessorException::FILTERED_EVENT_ONLY . json_encode($filteredEvents));
        }

        $registeredEvents = $this->eventRegister->retrieveEvent($event->getName());
        if (empty($registeredEvents)) {
            throw new ProcessorException(ProcessorException::NO_REGISTER_EVENTS . $event->getName());
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
                $service = $this->serviceFactory->createService($serviceName, $jsonSchema);
                if (empty($service)) {
                    continue;
                }

                if ($return === true) {
                    return $this->runService($event, $service, $callbacks, $return);
                }

                $this->runService($event, $service, $callbacks);
            }
        }

        return true;
    }

    /**
     * @param null $event
     * @return bool
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Main\Exception\ProcessorException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function rollback($event = null)
    {
        $event = $this->createEvent($event);
        $registeredEvents = $this->eventRegister->retrieveEvent($event->getName());
        if (empty($registeredEvents)) {
            throw new ProcessorException(ProcessorException::NO_REGISTER_EVENTS . $event->getName());
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
                $service = $this->serviceFactory->createService($serviceName, $jsonSchema);
                if (empty($service)) {
                    continue;
                }

                if ($service instanceof SagaInterface) {
                    $this->rollbackService($event, $service);
                }
            }
        }

        return true;
    }

    /**
     * @param $json
     * @return false|\BrighteCapital\ServiceSchema\Event\Event
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Main\Exception\ProcessorException
     */
    public function createEvent($json = null)
    {
        if ($json instanceof Event) {
            return $json;
        }

        $event = $this->eventFactory->createEvent($json);
        if (empty($event)) {
            throw new ProcessorException(ProcessorException::FAILED_TO_CREATE_event . $json);
        }

        return $event;
    }

    /**
     * @param \BrighteCapital\ServiceSchema\Event\EventInterface|null $event
     * @param \BrighteCapital\ServiceSchema\Service\SagaInterface|null $service
     * @return bool|\BrighteCapital\ServiceSchema\Event\EventInterface
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function rollbackService(EventInterface $event = null, SagaInterface $service = null)
    {
        $json = JsonReader::decode($event->toJson());
        $validator = $this->serviceValidator->validate($json, $service);
        if (!$validator->isValid()) {
            throw  new ServiceException(ServiceException::INVALIDATED_JSON_STRING . json_encode($validator->getErrors()));
        }

        if (isset($json->payload)) {
            $event->setPayload($json->payload);
        }

        return $service->rollback($event);
    }

    /**
     * @param \BrighteCapital\ServiceSchema\Event\EventInterface|null $event
     * @param \BrighteCapital\ServiceSchema\Service\ServiceInterface|null $service
     * @param array $callbacks
     * @param bool $return
     * @return bool
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function runService(EventInterface $event = null, ServiceInterface $service = null, array $callbacks = null, bool $return = false)
    {
        $json = JsonReader::decode($event->toJson());
        $validator = $this->serviceValidator->validate($json, $service);
        if (!$validator->isValid()) {
            throw  new ServiceException(ServiceException::INVALIDATED_JSON_STRING . json_encode($validator->getErrors()));
        }

        if (isset($json->payload)) {
            $event->setPayload($json->payload);
        }

        $result = $service->consume($event);
        if ($return === true) {
            return $result;
        }

        if (($result instanceof EventInterface) && !empty($callbacks)) {
            return $this->runCallbacks($result, $callbacks);
        }

        return $result;
    }

    /**
     * @param \BrighteCapital\ServiceSchema\Event\EventInterface|null $event
     * @param array|null $callbacks
     * @return bool
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function runCallbacks(EventInterface $event, array $callbacks = null)
    {
        if (empty($callbacks)) {
            return true;
        }

        foreach ($callbacks as $callback) {
            $service = $this->serviceFactory->createService($callback);
            if (empty($service)) {
                continue;
            }

            $service->consume($event);
        }

        return true;
    }

    /**
     * @return \BrighteCapital\ServiceSchema\Config\EventRegister
     */
    public function getEventRegister()
    {
        return $this->eventRegister;
    }

    /**
     * @param \BrighteCapital\ServiceSchema\Config\EventRegister $eventRegister
     * @return \BrighteCapital\ServiceSchema\Main\Processor
     */
    public function setEventRegister(EventRegister $eventRegister = null)
    {
        $this->eventRegister = $eventRegister;

        return $this;
    }

    /**
     * @return \BrighteCapital\ServiceSchema\Config\ServiceRegister
     */
    public function getServiceRegister()
    {
        return $this->serviceRegister;
    }

    /**
     * @param \BrighteCapital\ServiceSchema\Config\ServiceRegister $serviceRegister
     * @return \BrighteCapital\ServiceSchema\MainProcessor
     */
    public function setServiceRegister(ServiceRegister $serviceRegister = null)
    {
        $this->serviceRegister = $serviceRegister;

        return $this;
    }

    /**
     * @return \BrighteCapital\ServiceSchema\Event\EventFactory
     */
    public function getEventFactory()
    {
        return $this->eventFactory;
    }

    /**
     * @param \BrighteCapital\ServiceSchema\Event\EventFactory $eventFactory
     * @return \BrighteCapital\ServiceSchema\Main\Processor
     */
    public function setEventFactory(EventFactory $eventFactory = null)
    {
        $this->eventFactory = $eventFactory;

        return $this;
    }

    /**
     * @return \BrighteCapital\ServiceSchema\Service\ServiceFactory
     */
    public function getServiceFactory()
    {
        return $this->serviceFactory;
    }

    /**
     * @param \BrighteCapital\ServiceSchema\Service\ServiceFactory $serviceFactory
     * @return \BrighteCapital\ServiceSchema\Main\Processor
     */
    public function setServiceFactory(ServiceFactory $serviceFactory = null)
    {
        $this->serviceFactory = $serviceFactory;

        return $this;
    }

    /**
     * @return \BrighteCapital\ServiceSchema\Service\ServiceValidator
     */
    public function getServiceValidator()
    {
        return $this->serviceValidator;
    }

    /**
     * @param \BrighteCapital\ServiceSchema\Service\ServiceValidator $serviceValidator
     * @return \BrighteCapital\ServiceSchema\Main\Processor
     */
    public function setServiceValidator(ServiceValidator $serviceValidator = null)
    {
        $this->serviceValidator = $serviceValidator;

        return $this;
    }
}
