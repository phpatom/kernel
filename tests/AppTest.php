<?php


namespace Atom\Kernel\Test;

use Atom\Kernel\Kernel;
use Atom\Kernel\FileSystem\DiskManager;
use Atom\Kernel\FileSystem\Path;
use Atom\DI\DIC;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\Event\EventDispatcher;
use Atom\Event\Exceptions\ListenerAlreadyAttachedToEvent;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function testInstantiation()
    {
        $app = new Kernel(__DIR__);
        $this->assertInstanceOf(DIC::class, $app->container());
        $this->assertInstanceOf(Path::class, $app->path());
        $this->assertInstanceOf(EventDispatcher::class, $app->eventDispatcher());
        $this->assertInstanceOf(DiskManager::class, $app->disks());
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function testPaths()
    {
        $d = DIRECTORY_SEPARATOR;
        $app = new Kernel("foo");
        $this->assertEquals("foo" . $d . "bar" . $d . "baz", $app->path()->app("bar", "baz"));
    }
}
