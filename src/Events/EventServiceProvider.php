<?php


namespace Atom\App\Events;

use Atom\App\App;
use Atom\App\Contracts\ServiceProviderContract;
use Atom\Event\EventDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventServiceProvider implements ServiceProviderContract
{

    public function register(App $app)
    {
        $c = $app->container();
        $eventDispatcher = new EventDispatcher();
        $c->singletons()->store(
            EventDispatcherInterface::class,
            $c->as()->object($eventDispatcher)
        );
        $c->singletons()->store(
            EventDispatcher::class,
            $c->as()->object($eventDispatcher)
        );
    }
}
