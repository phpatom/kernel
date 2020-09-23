<?php


namespace Atom\App\Events;

use Atom\App\App;
use Atom\App\Contracts\ServiceProviderContract;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\Event\Contracts\EventDispatcherContract;
use Atom\Event\EventDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventServiceProvider implements ServiceProviderContract
{
    /**
     * @param App $app
     * @throws StorageNotFoundException
     */
    public function register(App $app)
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
