<?php


namespace Atom\App\FileSystem;

use Atom\App\App;
use Atom\App\Contracts\ServiceProviderContract;
use Atom\DI\Exceptions\StorageNotFoundException;

class PathProvider implements ServiceProviderContract
{
    /**
     * @var string
     */
    private $appPath;


    public function __construct(string $appPath)
    {

        $this->appPath = $appPath;
    }

    /**
     * @param App $app
     * @throws StorageNotFoundException
     */
    public function register(App $app)
    {
        $c = $app->container();
        $c->singletons()->store(
            Path::class,
            $c->as()->object(new Path($this->appPath))
        );
    }
}
