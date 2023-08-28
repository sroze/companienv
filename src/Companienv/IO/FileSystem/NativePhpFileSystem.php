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
        $relative = $this->isRelativePath($path);
        file_put_contents($this->isRelativePath($path) ? $this->realpath($path) : $path, $contents);
    }

    public function exists($path)
    {
        $relative = $this->isRelativePath($path);
        return file_exists($this->isRelativePath($path) ? $this->realpath($path) : $path);
    }

    public function getContents($path)
    {
        return file_get_contents($this->isRelativePath($path) ? $this->realpath($path) : $path);
    }

    public function delete($path)
    {
        return unlink($this->isRelativePath($path) ? $this->realpath($path) : $path);
    }

    public function realpath($path)
    {
        return $this->root . DIRECTORY_SEPARATOR . $path;
    }

    protected function isRelativePath($path)
    {
        return substr($path, 0, 1) !== '/';
    }
}
