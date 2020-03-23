<?php

namespace Micronative\ServiceSchema\Tests\Service\Samples;

use Micronative\ServiceSchema\Event\Message;
use Micronative\ServiceSchema\Event\MessageInterface;
use Micronative\ServiceSchema\Service\Service;
use Micronative\ServiceSchema\Service\ServiceInterface;

class CreateContact extends Service implements ServiceInterface
{
    public function consume(MessageInterface $message = null)
    {
        echo "CreateContact";

        return new Message();
    }
}
