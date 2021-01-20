<?php


namespace Atom\Kernel\Events;

use Atom\Kernel\Kernel;
use Atom\Kernel\Contracts\ServiceProviderContract;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\Event\Contracts\EventDispatcherContract;
use Atom\Event\EventDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventServiceProvider implements ServiceProviderContract
{
    /**
     * @param Kernel $app
     * @throws StorageNotFoundException
     */
    public function register(Kernel $app)
    {
        $c = $app->container();
        $eventDispatcher = new EventDispatcher();
        $c->singletons()->store(
            EventDispatcherInterface::class,
            $c->as()->object($eventDispatcher)
        );
        $c->singletons()->store(
            EventDispatcherContract::class,
            $c->as()->object($eventDispatcher)
        );
        $c->singletons()->store(
            EventDispatcher::class,
            $c->as()->object($eventDispatcher)
        );
    }
}
