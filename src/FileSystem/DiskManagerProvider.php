<?php
namespace Atom\Kernel\FileSystem;

use Atom\Kernel\Kernel;
use Atom\Kernel\Contracts\ServiceProviderContract;
use Atom\DI\Exceptions\StorageNotFoundException;

class DiskManagerProvider implements ServiceProviderContract
{
    /**
     * @var DiskContract[]
     */
    private $disks = [];

    public function __construct(array $disks)
    {
        $this->disks = $disks;
    }

    /**
     * @param Kernel $app
     * @throws StorageNotFoundException
     */
    public function register(Kernel $app)
    {
        $c = $app->container();
        $c->singletons()->store(DiskManager::class, $c->as()->object(new DiskManager($this->disks)));
    }
}
