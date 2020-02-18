<?php

namespace BrighteCapital\ServiceSchema\Service;

use BrighteCapital\ServiceSchema\Service\Exception\ServiceException;

class ServiceFactory
{
    /**
     * @param string|null $serviceClass
     * @param string|null $schema
     * @return \BrighteCapital\ServiceSchema\Service\ServiceInterface|false
     * @throws \BrighteCapital\ServiceSchema\Service\Exception\ServiceException
     */
    public function createService(string $serviceClass = null, string $schema = null)
    {
        try {
            $service = new $serviceClass();
        } catch (\Exception $exception) {
            throw new ServiceException(ServiceException::INVALID_SERVICE_CLASS . $serviceClass);
        }

        if ($service instanceof ServiceInterface) {
            $service->setName($serviceClass);
            $service->setJsonSchema($schema);

            return $service;
        }

        return false;
    }
}
