<?php

namespace BrighteCapital\ServiceSchema\Main;

use BrighteCapital\ServiceSchema\Config\EventRegister;
use BrighteCapital\ServiceSchema\Config\ServiceRegister;
use BrighteCapital\ServiceSchema\Event\Message;
use BrighteCapital\ServiceSchema\Event\MessageFactory;
use BrighteCapital\ServiceSchema\Event\MessageInterface;
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

    /** @var \BrighteCapital\ServiceSchema\Event\MessageFactory */
    protected $messageFactory;

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
        $this->messageFactory = new MessageFactory();
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
     * @param string|\BrighteCapital\ServiceSchema\Event\Message $message
     * @param bool $return return first service result
     * @param array|null $filteredEvents
     * @return bool
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     * @throws \BrighteCapital\ServiceSchema\Main\Exception\ProcessorException
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
                $service = $this->serviceFactory->createService($serviceName, $jsonSchema);
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
     * @param null $message
     * @return bool
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Main\Exception\ProcessorException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
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
                $service = $this->serviceFactory->createService($serviceName, $jsonSchema);
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
     * @param $json
     * @return false|\BrighteCapital\ServiceSchema\Event\Message
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Main\Exception\ProcessorException
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
     * @param \BrighteCapital\ServiceSchema\Event\MessageInterface|null $message
     * @param \BrighteCapital\ServiceSchema\Service\SagaInterface|null $service
     * @return bool|\BrighteCapital\ServiceSchema\Event\MessageInterface
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function rollbackService(MessageInterface $message = null, SagaInterface $service = null)
    {
        $json = JsonReader::decode($message->toJson());
        $validator = $this->serviceValidator->validate($json, $service);
        if (!$validator->isValid()) {
            throw  new ServiceException(ServiceException::INVALIDATED_JSON_STRING . json_encode($validator->getErrors()));
        }

        if (isset($json->payload)) {
            $message->setPayload($json->payload);
        }

        return $service->rollback($message);
    }

    /**
     * @param \BrighteCapital\ServiceSchema\Event\MessageInterface|null $message
     * @param \BrighteCapital\ServiceSchema\Service\ServiceInterface|null $service
     * @param array $callbacks
     * @param bool $return
     * @return bool
     * @throws \BrighteCapital\ServiceSchema\Json\Exception\JsonException
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function runService(MessageInterface $message = null, ServiceInterface $service = null, array $callbacks = null, bool $return = false)
    {
        $json = JsonReader::decode($message->toJson());
        $validator = $this->serviceValidator->validate($json, $service);
        if (!$validator->isValid()) {
            throw  new ServiceException(ServiceException::INVALIDATED_JSON_STRING . json_encode($validator->getErrors()));
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
     * @param \BrighteCapital\ServiceSchema\Event\MessageInterface|null $event
     * @param array|null $callbacks
     * @return bool
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function runCallbacks(MessageInterface $event, array $callbacks = null)
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
     * @return \BrighteCapital\ServiceSchema\Event\MessageFactory
     */
    public function getMessageFactory()
    {
        return $this->messageFactory;
    }

    /**
     * @param \BrighteCapital\ServiceSchema\Event\MessageFactory $messageFactory
     * @return \BrighteCapital\ServiceSchema\Main\Processor
     */
    public function setMessageFactory(MessageFactory $messageFactory = null)
    {
        $this->messageFactory = $messageFactory;

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
