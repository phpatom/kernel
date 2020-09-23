<?php


namespace Atom\App\Events;

use Atom\Event\AbstractEvent;

class ServiceProviderRegistered extends AbstractEvent
{
    /**
     * @var string
     */
    private $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

}
