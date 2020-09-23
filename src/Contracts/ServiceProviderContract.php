<?php


namespace Atom\App\Contracts;

use Atom\App\App;
use Atom\Event\EventDispatcher;

interface ServiceProviderContract
{
    public function register(App $app);
}
