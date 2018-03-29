<?php

namespace Companienv\Composer;

use Companienv\Application;
use Companienv\Companion;
use Companienv\Extension\Chained;
use Companienv\IO\FileSystem\NativePhpFileSystem;
use Composer\Script\Event;

class ScriptHandler
{
    public static function run(Event $event)
    {
        $extras = $event->getComposer()->getPackage()->getExtra();

        if (isset($extras['companienv-parameters'])) {
            $configs = $extras['companienv-parameters'];
        } else {
            $configs = [['file' => Application::defaultFile(), 'dist-file' => Application::defaultDistributionFile()]];
        }

        foreach ($configs as $config) {
            $directory = getcwd();

            $companion = new Companion(
                new NativePhpFileSystem($directory),
                new InteractionViaComposer($event->getIO()),
                new Chained(Application::defaultExtensions()),
                $config['file'],
                $config['dist-file']
            );
            $companion->fillGaps();
        }
    }
}
