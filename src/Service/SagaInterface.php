<?php

namespace Micronative\ServiceSchema\Service;

use Micronative\ServiceSchema\Event\MessageInterface;

interface SagaInterface
{

    /**
     * @param \Micronative\ServiceSchema\Event\MessageInterface $message
     * @return \Micronative\ServiceSchema\Event\MessageInterface|bool
     */
    public function rollback(MessageInterface $message = null);

}
