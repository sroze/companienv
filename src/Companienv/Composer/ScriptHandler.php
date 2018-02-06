<?php

namespace Companienv\Composer;

use Companienv\Application;
use Companienv\Companion;
use Companienv\Extension\Chained;
use Companienv\IO\FileSystem\NativePhpFileSystem;
use Companienv\IO\InputOutputInteraction;
use Composer\Script\Event;

class ScriptHandler
{
    public static function run(Event $event)
    {
        $directory = getcwd();

        $companion = new Companion(
            new NativePhpFileSystem($directory),
            new InteractionViaComposer($event->getIO()),
            new Chained(Application::defaultExtensions()),
            Application::defaultFile(),
            Application::defaultDistributionFile()
        );
        $companion->fillGaps();
    }
}
