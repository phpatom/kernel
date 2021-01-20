<?php


namespace Atom\Kernel\FileSystem;

class Path
{
    /**
     * @var string
     */
    private $publicPath;
    /**
     * @var string
     */
    private $appPath;

    public function __construct(string $appPath, ?string $publicPath = null)
    {
        $this->appPath = $this->removeTrailingSlash($appPath);
        if (!$publicPath) {
            $publicPath = $this->join($this->appPath, "public");
        }
        $this->publicPath = $this->removeTrailingSlash($publicPath);
    }

    public function join(...$paths): string
    {
        return implode(DIRECTORY_SEPARATOR, $paths ?? []);
    }

    /**
     * @param array $paths
     * @return string
     */
    public function app(...$paths): string
    {
        if (!empty($paths)) {
            return $this->join(...array_merge([$this->appPath], $paths));
        }
        return $this->appPath;
    }

    private function removeTrailingSlash(string $path): string
    {
        return rtrim(rtrim($path, "/"), "\\");
    }
}
