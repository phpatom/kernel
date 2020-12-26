<?php


namespace Atom\App\FileSystem;

use League\Flysystem\Filesystem;

class DiskManager
{
    /**
     * @var DiskManager
     */
    private static $instance;

    /**
     * DiskManager constructor.
     * @param DiskContract[] $disks
     */
    public function __construct(array $disks = [])
    {
        foreach ($disks as $disk) {
            $this->add($disk);
        }
        self::$instance = $this;
    }

    /**
     * @var Filesystem []
     */
    private $disks = [];

    public function add(DiskContract $disk)
    {
        $this->disks[$disk->getLabel()] = new Filesystem($disk->getAdapter(), $disk->getConfig());
    }

    /**
     * @param string $label
     * @return Filesystem
     * @throws DiskNotFoundException
     */
    public function get(string $label): Filesystem
    {
        if (!$this->has($label)) {
            throw new DiskNotFoundException("the disk [$label] was not found");
        }
        return $this->disks[$label];
    }

    public function has(string $label): bool
    {
        return array_key_exists($label, $this->disks);
    }

    /**
     * @param $label
     * @throws DiskNotFoundException
     */
    public function remove($label)
    {
        if (!$this->has($label)) {
            throw new DiskNotFoundException("the disk [$label] was not found");
        }
        unset($this->disks[$label]);
    }

    /**
     * @return DiskManager
     * @throws DiskManagerException
     */
    public static function instance(): DiskManager
    {
        if (is_null(self::$instance)) {
            throw new DiskManagerException("No instance of the disk manager was found");
        }
        return self::$instance;
    }

    /**
     * @param string $label
     * @return Filesystem
     * @throws DiskManagerException
     * @throws DiskNotFoundException
     */
    public function disk(string $label): Filesystem
    {
        return self::instance()->get($label);
    }
}
