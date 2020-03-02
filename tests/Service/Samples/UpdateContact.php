<?php

namespace BrighteCapital\ServiceSchema\Tests\Service\Samples;

use BrighteCapital\ServiceSchema\Event\EventInterface;
use BrighteCapital\ServiceSchema\Service\Service;
use BrighteCapital\ServiceSchema\Service\ServiceInterface;

class UpdateContact extends Service implements ServiceInterface
{
    public function consume(EventInterface $event = null)
    {
        echo "UpdateContact";

        return true;
    }
}
