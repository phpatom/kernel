<?php

namespace Atom\App;

use Atom\App\Contracts\ServiceProviderContract;
use Atom\App\Events\Booted;
use Atom\App\Events\EventServiceProvider;
use Atom\App\Events\ServiceProviderRegistered;
use Atom\App\FileSystem\DiskManager;
use Atom\App\FileSystem\DiskManagerProvider;
use Atom\App\FileSystem\Disks\Local;
use Atom\App\FileSystem\Path;
use Atom\App\FileSystem\PathProvider;
use Atom\DI\DIC;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\Event\EventDispatcher;
use Atom\Event\Exceptions\ListenerAlreadyAttachedToEvent;

/**
 * Class App
 * @package Atom\App
 */
class App
{
    /**
     * @var Path
     */
    private $path;

    /**
     * @var DIC
     */
    private $container;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var DiskManager
     */
    private $disks;

    /**
     * App constructor.
     * @param string $appDir
     * @param string|null $publicDir
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws ListenerAlreadyAttachedToEvent
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function __construct(string $appDir, ?string $publicDir = null)
    {
        $this->boot($appDir, $publicDir);
    }

    /**
     * @return EventDispatcher
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function eventDispatcher(): EventDispatcher
    {
        if ($this->eventDispatcher == null) {
            $this->eventDispatcher = $this->container()->get(EventDispatcher::class);
        }
        return $this->eventDispatcher;
    }

    /**
     * @param ServiceProviderContract $serviceProvider
     * @return $this
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws ListenerAlreadyAttachedToEvent
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function use(ServiceProviderContract $serviceProvider): self
    {
        $serviceProvider->register($this);
        $this->eventDispatcher()->dispatch(new ServiceProviderRegistered(get_class($serviceProvider)));
        return $this;
    }

    /**
     * @return DIC
     */
    public function container(): DIC
    {
        return $this->container;
    }

    /**
     * @return Path
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function path(): Path
    {
        if (!$this->path) {
            $this->path = $this->container()->get(Path::class);
        }
        return $this->path;
    }

    /**
     * @return DiskManager
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function disks(): DiskManager
    {
        if (!$this->disks) {
            $this->disks = $this->container()->get(DiskManager::class);
        }
        return $this->disks;
    }

    /**
     * @param string $appDir
     * @param string|null $publicDir
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws ListenerAlreadyAttachedToEvent
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    private function boot(string $appDir, ?string $publicDir)
    {
        $this->container = new DIC();
        $this->use(new EventServiceProvider());
        $this->use(new PathProvider($appDir, $publicDir));
        $this->use(new DiskManagerProvider([
            new Local($this->path()->app(), "app"),
            new Local($this->path()->public(), "public"),
        ]));
    }

}
