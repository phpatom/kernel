<?php


namespace Atom\App\Test;

use Atom\App\App;
use Atom\App\FileSystem\DiskManager;
use Atom\App\FileSystem\Path;
use Atom\DI\DIC;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\Event\EventDispatcher;
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
        $app = new App(__DIR__);
        $this->assertInstanceOf(DIC::class, $app->container());
        $this->assertInstanceOf(Path::class, $app->path());
        $this->assertInstanceOf(EventDispatcher::class, $app->eventDispatcher());
        $this->assertInstanceOf(DiskManager::class, $app->disks());
    }

    public function testPaths()
    {
        $app = new App("foo");
        $this->assertEquals($app->path()->app("bar", "baz"), "foo/bar/baz");
        $this->assertEquals($app->path()->public("bar", "baz"), "public/bar/baz");
        $app = new App("foo", "public/foo/");
        $this->assertEquals($app->path()->public("bar", "baz"), "public/foo/bar/baz");
    }
}
