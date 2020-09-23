<?php


namespace Atom\App\FileSystem;

use League\Flysystem\AdapterInterface;

interface DiskContract
{
    public function getAdapter():AdapterInterface;
    public function getLabel():string;
    public function getConfig():?array;
}
