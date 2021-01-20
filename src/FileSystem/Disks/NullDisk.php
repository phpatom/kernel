<?php


namespace Atom\Kernel\FileSystem\Disks;


use Atom\Kernel\FileSystem\DiskContract;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\AdapterInterface;

class NullDisk implements DiskContract
{
    /**
     * @var string
     */
    private $label;
    /**
     * @var array
     */
    private $config;

    public function __construct(string $label, ?array $config = null)
    {
        $this->label = $label;
        $this->config = $config;
    }

    public function getAdapter(): AdapterInterface
    {
        return new NullAdapter();
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getConfig(): ?array
    {
        return $this->config;
    }
}