<?php

namespace Companienv;

use Companienv\DotEnv\Attribute;
use Companienv\DotEnv\Block;
use Companienv\DotEnv\File;
use Companienv\Extension\Chained;
use Jackiedo\DotenvEditor\DotenvFormatter;
use Jackiedo\DotenvEditor\DotenvWriter;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Companion
{
    private $input;
    private $output;

    private $reference;
    private $path;

    private $extension;

    public function __construct(InputInterface $input, OutputInterface $output, File $reference, string $path, Extension $extension)
    {
        $this->input = $input;
        $this->output = $output;

        $this->reference = $reference;
        $this->path = $path;

        $this->extension = $extension;
    }

    public function fillGaps()
    {
        $missingVariables = $this->getMissingVariables();
        if (count($missingVariables) == 0) {
            return;
        }

        $this->output->writeln(sprintf(
            'It looks like you are missing some configuration (%d variables). I will help you to sort this out.',
            count($missingVariables)
        ));

        if (!$this->askConfirmation('<info>Let\'s fix this? (y) </info>')) {
            $this->output->writeln([
                '',
                '<comment>I let you think about it then. Re-run the command to get started again.</comment>',
                ''
            ]);

            return;
        }

        foreach ($this->reference->getBlocks() as $block) {
            $this->fillBlockGaps($block, $missingVariables);
        }
    }

    private function fillBlockGaps(Block $block, array $missingVariables)
    {
        $variablesInBlock = $block->getVariablesInBlock($missingVariables);
        if (count($variablesInBlock) == 0) {
            return;
        }

        $this->output->writeln([
            '',
            '<info>'.$block->getTitle().'</info>',
            $block->getDescription(),
            ''
        ]);

        foreach ($block->getVariables() as $variable) {
            $this->writeVariable($variable->getName(), $this->extension->getVariableValue($this, $block, $variable));
        }
    }

    private function writeVariable(string $name, string $value)
    {
        if (!file_exists($this->path)) {
            file_put_contents($this->path, '');
        }

        $variablesInFileHash = $this->getDefinedVariablesHash();

        $writer = new DotenvWriter(new DotenvFormatter());
        $writer->setBuffer(file_get_contents($this->path));

        if (isset($variablesInFileHash[$name])) {
            $writer->updateSetter($name, $value);
        } else {
            $writer->appendSetter($name, $value);
        }

        $writer->save($this->path);
    }

    private function getMissingVariables()
    {
        $variablesInFile = $this->getDefinedVariablesHash();

        $variablesInReference = $this->reference->getAllVariables();
        $missingVariables = [];

        foreach ($variablesInReference as $variable) {
            if (!isset($variablesInFile[$variable->getName()])) {
                $missingVariables[] = $variable;
            }
        }

        return $missingVariables;
    }

    public function getDefinedVariablesHash()
    {
        $variablesInFile = [];
        if (file_exists($this->path)) {
            $dotEnv = new \Symfony\Component\Dotenv\Dotenv();
            $variablesInFile = $dotEnv->parse(file_get_contents($this->path), $this->path);
        }

        return $variablesInFile;
    }

    public function askConfirmation(string $question) : bool
    {
        return in_array(strtolower($this->ask($question, 'y')), ['y', 'yes']);
    }

    public function ask(string $question, string $default = null) : string
    {
        return (new QuestionHelper())->ask($this->input, $this->output, new Question($question, $default));
    }
}
