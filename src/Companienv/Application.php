<?php

namespace Companienv;

use Companienv\DotEnv\Parser;
use Companienv\Extension\Chained;
use Companienv\Extension\FileToPropagate;
use Companienv\Extension\RsaKeys;
use Companienv\Extension\SslCertificate;
use Companienv\Interaction\AskVariableValues;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Application
{
    private $rootDirectory;

    /** @var Extension[] */
    private $extensions = [];

    public function __construct(string $rootDirectory, array $extensions = null)
    {
        $this->rootDirectory = $rootDirectory;
        $this->extensions = $extensions !== null ? $extensions : [
            new SslCertificate($rootDirectory),
            new RsaKeys($rootDirectory),
            new FileToPropagate($rootDirectory),
            new AskVariableValues(),
        ];
    }

    public function run()
    {
        $referenceFile = $this->rootDirectory.'/.env.dist';
        $configurationFile = $this->rootDirectory.'/.env';

        $reference = (new Parser())->parse($referenceFile);

        $input = new ArgvInput();
        $output = new ConsoleOutput();

        $companion = new Companion($input, $output, $reference, $configurationFile, new Chained($this->extensions));
        $companion->fillGaps();
    }

    public function registerExtension(Extension $extension)
    {
        array_unshift($this->extensions, $extension);
    }
}
