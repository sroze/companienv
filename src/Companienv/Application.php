<?php

namespace Companienv;

use Companienv\Extension\Chained;
use Companienv\Extension\FileToPropagate;
use Companienv\Extension\OnlyIf;
use Companienv\Extension\RsaKeys;
use Companienv\Extension\SslCertificate;
use Companienv\IO\FileSystem\NativePhpFileSystem;
use Companienv\Interaction\AskVariableValues;
use Companienv\IO\InputOutputInteraction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        $this->extensions = $extensions !== null ? $extensions : self::defaultExtensions();

        $this->add(new class([$this, 'companion'], 'companion') extends Command {
            private $callable;

            public function __construct(callable $callable, $name)
            {
                parent::__construct($name);

                $this->callable = $callable;

                $this->addOption('dist-file', null, InputOption::VALUE_REQUIRED, 'Name of the file used as reference', Application::defaultDistributionFile());
                $this->addOption('file', null, InputOption::VALUE_REQUIRED, 'Name of the file used for the values', Application::defaultFile());
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
        $companion = new Companion(
            new NativePhpFileSystem($this->rootDirectory),
            new InputOutputInteraction($input, $output),
            new Chained($this->extensions),
            $input->getOption('file'),
            $input->getOption('dist-file')
        );
        $companion->fillGaps();
        return 0;
    }

    public function registerExtension(Extension $extension)
    {
        array_unshift($this->extensions, $extension);
    }

    public static function defaultExtensions()
    {
        return [
            new OnlyIf(),
            new SslCertificate(),
            new RsaKeys(),
            new FileToPropagate(),
            new AskVariableValues(),
        ];
    }

    public static function defaultFile()
    {
        return '.env';
    }

    public static function defaultDistributionFile()
    {
        return '.env.dist';
    }
}
