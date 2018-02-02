<?php

namespace Companienv\Composer;

use Symfony\Component\Process\Process;

class ScriptHandler
{
    public static function run()
    {
        $directory = getcwd();

        if (!file_exists($path = implode(DIRECTORY_SEPARATOR, array($directory, 'bin', 'companienv')))) {
            if (!file_exists($path = implode(DIRECTORY_SEPARATOR, array($directory, 'vendor', 'bin', 'companienv')))) {
                throw new \RuntimeException('Could not find Companienv\'s console');
            }
        }

        (new Process($path, getcwd()))->run();
    }
}
