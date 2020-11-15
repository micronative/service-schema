<?php

namespace Micronative\ServiceSchema\Tests\Service\Samples;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\ServiceInterface;

class UpdateContact extends AbstractService implements ServiceInterface
{
    public function consume(AbstractEvent $event = null)
    {
        echo "UpdateContact";

        return true;
    }
}
