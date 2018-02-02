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

    public function exists($path)
    {
        return file_exists($this->realpath($path));
    }

    public function getContents($path)
    {
        return file_get_contents($this->realpath($path));
    }

    public function realpath($path)
    {
        if (strpos($path, DIRECTORY_SEPARATOR) === 0) {
            return $path;
        }

        return $this->root.DIRECTORY_SEPARATOR.$path;
    }
}
