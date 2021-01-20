<?php

namespace Atom\Kernel;

use Atom\Kernel\Contracts\ServiceProviderContract;
use Atom\Kernel\Env\Env;
use Atom\Kernel\Events\EventServiceProvider;
use Atom\Kernel\Events\ServiceProviderRegistered;
use Atom\Kernel\FileSystem\DiskManager;
use Atom\Kernel\FileSystem\DiskManagerProvider;
use Atom\Kernel\FileSystem\Disks\Local;
use Atom\Kernel\FileSystem\Disks\NullDisk;
use Atom\Kernel\FileSystem\Path;
use Atom\Kernel\FileSystem\PathProvider;
use Atom\DI\DIC;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\Event\EventDispatcher;
use Atom\Event\Exceptions\ListenerAlreadyAttachedToEvent;

/**
 * Class Kernel
 * @package Atom\Kernel
 */
class Kernel
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
     * @var Env
     */
    private $env;

    /**
     * Kernel constructor.
     * @param string $env
     * @param string $appDir
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws ListenerAlreadyAttachedToEvent
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function __construct(string $appDir, string $env = Env::DEV)
    {
        $this->boot($appDir, $env);
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
     * @return Env
     */
    public function env(): Env
    {
        return $this->env;
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
     * @param string $env
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws ListenerAlreadyAttachedToEvent
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    private function boot(string $appDir, string $env)
    {
        $this->container = new DIC();
        $this->use(new EventServiceProvider());
        $this->use(new PathProvider($appDir));
        $this->use(new DiskManagerProvider([]));

        $this->env = Env::create($this->path()->app(), $env);
        $this->container->singletons()->bindInstance($this->env);
    }

    /**
     * @param array $permissions
     * @param array|null $config
     * @param int $writeFlags
     * @param int $linkHandling
     * @return Kernel
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function useAppDisk(
        array $permissions = [],
        ?array $config = null,
        int $writeFlags = LOCK_EX,
        int $linkHandling = Local::DISALLOW_LINKS
    ): Kernel {
        $this->addLocalDisk("/", "app", $permissions, $config, $writeFlags, $linkHandling);
        return $this;
    }

    /**
     * @param string $path
     * @param string $label
     * @param array $permissions
     * @param array|null $config
     * @param int $writeFlags
     * @param int $linkHandling
     * @return Kernel
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function addLocalDisk(
        string $path,
        string $label,
        array $permissions = [],
        ?array $config = null,
        int $writeFlags = LOCK_EX,
        int $linkHandling = Local::DISALLOW_LINKS
    ): Kernel {
        if ($this->env()->isTesting()) {
            $this->disks()->add(new NullDisk($label, $config));
        }
        $this->disks()
            ->add(
                (new Local($this->path()->app($path), $label, $permissions, $writeFlags, $linkHandling))
                    ->withConfig($config)
            );
        return $this;
    }
}
