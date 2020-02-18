<?php

namespace BrighteCapital\ServiceSchema\Tests\Service\Samples;

use BrighteCapital\ServiceSchema\Event\Message;
use BrighteCapital\ServiceSchema\Event\MessageInterface;
use BrighteCapital\ServiceSchema\Service\Service;
use BrighteCapital\ServiceSchema\Service\ServiceInterface;

class CreateContact extends Service implements ServiceInterface
{
    public function consume(MessageInterface $message = null)
    {
        echo "CreateContact";

        return new Message();
    }
}
