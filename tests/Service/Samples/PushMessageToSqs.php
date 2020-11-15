<?php

namespace Micronative\ServiceSchema\Tests\Service\Samples;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\ServiceInterface;

class PushMessageToSqs extends AbstractService implements ServiceInterface
{
    public function consume(AbstractEvent $event = null)
    {
        echo "Push message to SQS";

        return true;
    }
}
