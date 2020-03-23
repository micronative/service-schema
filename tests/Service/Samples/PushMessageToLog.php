<?php

namespace Micronative\ServiceSchema\Tests\Service\Samples;

use Micronative\ServiceSchema\Event\MessageInterface;
use Micronative\ServiceSchema\Service\Service;
use Micronative\ServiceSchema\Service\ServiceInterface;

class PushMessageToLog extends Service implements ServiceInterface
{
    public function consume(MessageInterface $message = null)
    {
        echo "Push message to Log";

        return true;
    }
}
