<?php


namespace Atom\Kernel\Contracts;

use Atom\Kernel\Kernel;
use Atom\Event\EventDispatcher;

interface ServiceProviderContract
{
    public function register(Kernel $app);
}
