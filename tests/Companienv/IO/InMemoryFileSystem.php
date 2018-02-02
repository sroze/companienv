<?php

namespace Companienv\IO;

use Companienv\IO\FileSystem\FileSystem;

class InMemoryFileSystem implements FileSystem
{
    private $files = [];

    public function write($path, string $contents)
    {
        $this->files[$path] = $contents;
    }

    public function exists($path, bool $relative = true)
    {
        return isset($this->files[$path]);
    }

    public function getContents($path, bool $relative = true)
    {
        if (!$this->exists($path)) {
            return false;
        }

        return $this->files[$path];
    }

    public function realpath($path)
    {
        return sys_get_temp_dir().$path;
    }
}
