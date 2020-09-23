<?php


namespace Atom\App\FileSystem\Disks;

use Atom\App\FileSystem\DiskContract;
use League\Flysystem\AdapterInterface;

class Local implements DiskContract
{
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
     * Local constructor.
     * @param string $path
     * @param String $label
     */
    public function __construct(string $path, string $label)
    {
        $this->path = $path;
        $this->label = $label;
    }

    public function getAdapter(): AdapterInterface
    {
        return new \League\Flysystem\Adapter\Local($this->path);
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
