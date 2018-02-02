<?php

namespace Companienv\IO\FileSystem;

class NativePhpFileSystem implements FileSystem
{
    private $root;

    public function __construct(string $root)
    {
        $this->root = $root;
    }

    public function write($path, string $contents)
    {
        file_put_contents($this->realpath($path), $contents);
    }

    public function exists($path, bool $relative = true)
    {
        return file_exists($relative ? $this->realpath($path) : $path);
    }

    public function getContents($path, bool $relative = true)
    {
        return file_get_contents($relative ? $this->realpath($path) : $path);
    }

    public function realpath($path)
    {
        return $this->root.DIRECTORY_SEPARATOR.$path;
    }
}
