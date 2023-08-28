<?php

namespace Companienv\IO\FileSystem;

interface FileSystem
{
    public function write($path, string $contents);

    public function exists($path);

    public function getContents($path);

    public function realpath($path);
}
