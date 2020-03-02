<?php

namespace BrighteCapital\ServiceSchema\Tests\Service\Samples;

use BrighteCapital\ServiceSchema\Event\EventInterface;
use BrighteCapital\ServiceSchema\Service\Service;
use BrighteCapital\ServiceSchema\Service\ServiceInterface;

class PushEventToLog extends Service implements ServiceInterface
{
    public function consume(EventInterface $event = null)
    {
        echo "Push message to Log";

        return true;
    }
}
