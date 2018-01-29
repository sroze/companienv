<?php

namespace Companienv;

use Companienv\DotEnv\Parser;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Application
{
    private $rootDirectory;

    public function __construct(string $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function run()
    {
        $referenceFile = $this->rootDirectory.'/.env.dist';
        $configurationFile = $this->rootDirectory.'/.env';

        $reference = (new Parser())->parse($referenceFile);

        $companion = new Companion(new ArgvInput(), new ConsoleOutput(), $reference, $configurationFile);
        $companion->fillGaps();
    }
}
