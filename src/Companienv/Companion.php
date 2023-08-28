<?php

namespace Companienv;

use Companienv\DotEnv\Block;
use Companienv\DotEnv\MissingVariable;
use Companienv\DotEnv\Parser;
use Companienv\IO\FileSystem\FileSystem;
use Companienv\IO\Interaction;
use Jackiedo\DotenvEditor\DotenvReader;
use Jackiedo\DotenvEditor\DotenvWriter;
use Jackiedo\DotenvEditor\Workers\Formatters\Formatter;
use Jackiedo\DotenvEditor\Workers\Parsers\ParserV3;

class Companion
{
    private $fileSystem;
    private $interaction;
    private $reference;
    private $extension;
    private $envFileName;

    public function __construct(FileSystem $fileSystem, Interaction $interaction, Extension $extension, string $envFileName = '.env', string $distFileName = '.env.dist')
    {
        $this->fileSystem = $fileSystem;
        $this->interaction = $interaction;
        $this->extension = $extension;
        $this->reference = (new Parser())->parse($fileSystem, $distFileName);
        $this->envFileName = $envFileName;
    }

    public function fillGaps()
    {
        $missingVariables = $this->getVariablesRequiringValues();
        if (count($missingVariables) === 0) {
            return;
        }

        $this->interaction->writeln(sprintf(
            'It looks like you are missing some configuration (%d variables). I will help you to sort this out.',
            count($missingVariables)
        ));

        if (!$this->askConfirmation('<info>Let\'s fix this? (y) </info>')) {
            $this->interaction->writeln([
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

        if (!empty($title = $block->getTitle())) {
            $this->interaction->writeln([
                '',
                '<info>' . $block->getTitle() . '</info>',
            ]);
        }

        if (!empty($description = $block->getDescription())) {
            $this->interaction->writeln($description);
        }

        $this->interaction->writeln('');

        foreach ($block->getVariables() as $variable) {
            if (isset($missingVariables[$variable->getName()])) {
                $this->writeVariable($variable->getName(), $this->extension->getVariableValue($this, $block, $variable));
            }
        }
    }

    private function writeVariable(string $name, string $value)
    {
        if (!$this->fileSystem->exists($this->envFileName)) {
            $this->fileSystem->write($this->envFileName, '');
        }

        $variablesInFileHash = $this->getDefinedVariablesHash();

        $writer = new DotenvWriter(new Formatter());
        $reader = (new DotenvReader(new ParserV3()))->load($this->envFileName);
        foreach ($reader->entries(true) as $entry) {
            if (isset($entry['parsed_data'])) {
                $writer->appendSetter(
                    $entry['parsed_data']['key'],
                    trim($entry['parsed_data']['value'], '"'),
                    $entry['parsed_data']['comment'],
                    $entry['parsed_data']['export']
                );
            }
        }

        if (isset($variablesInFileHash[$name])) {
            $writer->updateSetter($name, $value);
        } else {
            $writer->appendSetter($name, $value);
        }

        $this->fileSystem->write($this->envFileName, $writer->getBuffer(false));
    }

    /**
     * @return MissingVariable[]
     */
    private function getVariablesRequiringValues()
    {
        $variablesInFile = $this->getDefinedVariablesHash();
        $missingVariables = [];

        foreach ($this->reference->getBlocks() as $block) {
            foreach ($block->getVariables() as $variable) {
                $currentValue = isset($variablesInFile[$variable->getName()]) ? $variablesInFile[$variable->getName()] : null;

                if ($this->extension->isVariableRequiringValue($this, $block, $variable, $currentValue) === Extension::VARIABLE_REQUIRED) {
                    $missingVariables[$variable->getName()] = new MissingVariable($variable, $currentValue);
                }
            }
        }

        return $missingVariables;
    }

    public function getDefinedVariablesHash()
    {
        $variablesInFile = [];
        if ($this->fileSystem->exists($this->envFileName)) {
            $dotEnv = new \Symfony\Component\Dotenv\Dotenv();
            $variablesInFile = $dotEnv->parse($this->fileSystem->getContents($this->envFileName), $this->envFileName);
        }

        return $variablesInFile;
    }

    public function askConfirmation(string $question) : bool
    {
        return $this->interaction->askConfirmation($question);
    }

    public function ask(string $question, string $default = null) : string
    {
        return $this->interaction->ask($question, $default);
    }

    public function getFileSystem(): FileSystem
    {
        return $this->fileSystem;
    }
}
