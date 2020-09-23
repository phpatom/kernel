<?php
namespace Atom\App\FileSystem;

use Atom\App\App;
use Atom\App\Contracts\ServiceProviderContract;
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
     * @param App $app
     * @throws StorageNotFoundException
     */
    public function register(App $app)
    {
        $c = $app->container();
        $c->singletons()->store(DiskManager::class, $c->as()->object(new DiskManager($this->disks)));
    }
}
