<?php

namespace BrighteCapital\ServiceSchema\Service;

use BrighteCapital\ServiceSchema\Event\EventInterface;

interface SagaInterface
{

    /**
     * @param \BrighteCapital\ServiceSchema\Event\EventInterface $event
     * @return \BrighteCapital\ServiceSchema\Event\EventInterface|bool
     */
    public function rollback(EventInterface $event = null);

}
