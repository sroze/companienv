<?php

namespace Companienv\IO\FileSystem;

interface FileSystem
{
    public function write($path, string $contents);

    public function exists($path, bool $relative = true);

    public function getContents($path, bool $relative = true);

    public function realpath($path);
}
