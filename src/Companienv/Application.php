<?php

namespace Companienv;

use Companienv\DotEnv\Parser;
use Companienv\Extension\Chained;
use Companienv\Extension\FileToPropagate;
use Companienv\Extension\RsaKeys;
use Companienv\Extension\SslCertificate;
use Companienv\Interaction\AskVariableValues;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{
    private $rootDirectory;

    /** @var Extension[] */
    private $extensions = [];

    public function __construct(string $rootDirectory, array $extensions = null)
    {
        parent::__construct('Companienv', '0.0.x-dev');

        $this->rootDirectory = $rootDirectory;
        $this->extensions = $extensions !== null ? $extensions : [
            new SslCertificate($rootDirectory),
            new RsaKeys($rootDirectory),
            new FileToPropagate($rootDirectory),
            new AskVariableValues(),
        ];

        $this->add(new class([$this, 'companion'], 'companion') extends Command {
            private $callable;

            public function __construct(callable $callable, $name)
            {
                parent::__construct($name);

                $this->callable = $callable;
            }

            protected function execute(InputInterface $input, OutputInterface $output)
            {
                $callable = $this->callable;

                return $callable($input, $output);
            }
        });
        
        $this->setDefaultCommand('companion', true);
    }

    public function companion(InputInterface $input, OutputInterface $output)
    {
        $referenceFile = $this->rootDirectory.'/.env.dist';
        $configurationFile = $this->rootDirectory.'/.env';

        $reference = (new Parser())->parse($referenceFile);

        $companion = new Companion($input, $output, $reference, $configurationFile, new Chained($this->extensions));
        $companion->fillGaps();
    }

    public function registerExtension(Extension $extension)
    {
        array_unshift($this->extensions, $extension);
    }
}
