<?php

namespace BrighteCapital\ServiceSchema\Tests\Service\Samples;

use BrighteCapital\ServiceSchema\Event\MessageInterface;
use BrighteCapital\ServiceSchema\Service\Service;
use BrighteCapital\ServiceSchema\Service\ServiceInterface;

class PushMessageToSqs extends Service implements ServiceInterface
{
    public function consume(MessageInterface $message = null)
    {
        echo "Push message to SQS";

        return true;
    }
}
