<?php


namespace Micronative\ServiceSchema\Tests\Service\Samples;


use Micronative\ServiceSchema\Event\MessageInterface;
use Micronative\ServiceSchema\Service\Service;
use Micronative\ServiceSchema\Service\ServiceInterface;

class CreateTask extends Service implements ServiceInterface
{
    public function consume(MessageInterface $message = null)
    {
        echo "CreateTask";

        return true;
    }
}
