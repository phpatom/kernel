<?php


namespace Atom\App\FileSystem;

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
        if (!$publicPath) {
            $publicPath = $this->join($this->appPath, "public");
        }
        $this->publicPath = $this->removeTrailingSlash($publicPath);
        $this->appPath = $this->removeTrailingSlash($appPath);
    }

    /**
     * @param array $paths
     * @return string
     */
    public function public(...$paths): string
    {
        if (!empty($paths)) {
            return $this->join(...array_merge([$this->publicPath], $paths));
        }
        return $this->publicPath;
    }

    public function join(...$paths)
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

    private function removeTrailingSlash(string $path)
    {
        return trim(trim($path, "/"), "\\");
    }
}
