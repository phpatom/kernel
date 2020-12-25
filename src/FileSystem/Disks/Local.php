<?php


namespace Atom\App\FileSystem\Disks;

use Atom\App\FileSystem\DiskContract;
use League\Flysystem\AdapterInterface;

class Local implements DiskContract
{
    /**
     * @var int
     */
    const SKIP_LINKS = 0001;

    /**
     * @var int
     */
    const DISALLOW_LINKS = 0002;


    /**
     * @var array
     */
    private $config;
    /**
     * @var string
     */
    private $path;

    private $label;
    /**
     * @var int
     */
    private $writeFlags;
    /**
     * @var int
     */
    private $linkHandling;
    /**
     * @var array
     */
    private $permissions;

    /**
     * Local constructor.
     * @param string $path
     * @param String $label
     * @param int $writeFlags
     * @param int $linkHandling
     * @param array $permissions
     */
    public function __construct(
        string $path,
        string $label,
        array $permissions = [],
        int $writeFlags = LOCK_EX,
        int $linkHandling = self::DISALLOW_LINKS
    ) {
        $this->path = $path;
        $this->label = $label;
        $this->permissions = $permissions;
        $this->writeFlags = $writeFlags;
        $this->linkHandling = $linkHandling;
    }

    public function getAdapter(): AdapterInterface
    {
        return new \League\Flysystem\Adapter\Local(
            $this->path,
            $this->writeFlags,
            $this->linkHandling,
            $this->permissions
        );
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getConfig(): ?array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function withConfig(array $config): void
    {
        $this->config = $config;
    }
}
