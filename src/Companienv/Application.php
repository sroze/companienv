<?php

namespace Companienv;

use Companienv\DotEnv\Parser;
use Companienv\Extension\Chained;
use Companienv\Interaction\AskVariableValues;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Application
{
    private $rootDirectory;

    /** @var Extension[] */
    private $extensions = [];

    public function __construct(string $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function run()
    {
        $referenceFile = $this->rootDirectory.'/.env.dist';
        $configurationFile = $this->rootDirectory.'/.env';

        $reference = (new Parser())->parse($referenceFile);

        $input = new ArgvInput();
        $output = new ConsoleOutput();

        $this->extensions[] = new AskVariableValues();

        $companion = new Companion($input, $output, $reference, $configurationFile, new Chained($this->extensions));
        $companion->fillGaps();
    }

    public function registerExtension(Extension $extension)
    {
        $this->extensions[] = $extension;
    }
}
