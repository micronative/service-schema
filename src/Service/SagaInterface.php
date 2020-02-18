<?php

namespace BrighteCapital\ServiceSchema\Service;

use BrighteCapital\ServiceSchema\Event\MessageInterface;

interface SagaInterface
{

    /**
     * @param \BrighteCapital\ServiceSchema\Event\MessageInterface $message
     * @return \BrighteCapital\ServiceSchema\Event\MessageInterface|bool
     */
    public function rollback(MessageInterface $message = null);

}
