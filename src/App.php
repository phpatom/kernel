<?php

namespace Atom\App;

use Atom\App\Contracts\ServiceProviderContract;
use Atom\App\Env\Env;
use Atom\App\Events\EventServiceProvider;
use Atom\App\Events\ServiceProviderRegistered;
use Atom\App\FileSystem\DiskManager;
use Atom\App\FileSystem\DiskManagerProvider;
use Atom\App\FileSystem\Disks\Local;
use Atom\App\FileSystem\Disks\NullDisk;
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
     * @var Env
     */
    private $env;

    /**
     * App constructor.
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
     * @return App
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function useAppDisk(array $permissions = [], ?array $config = null, int $writeFlags = LOCK_EX, int $linkHandling = Local::DISALLOW_LINKS): App
    {
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
     * @return App
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
    ): App
    {
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
