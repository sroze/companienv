<?php

namespace Companienv\Extension;

use Companienv\Companion;
use Companienv\DotEnv\Block;
use Companienv\DotEnv\Variable;
use Companienv\Extension;

class FileToPropagate implements Extension
{
    private $rootDirectory;

    public function __construct(string $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariableValue(Companion $companion, Block $block, Variable $variable)
    {
        if (null === ($attribute = $block->getAttribute('file-to-propagate')) || !in_array($variable->getName(), $attribute->getVariableNames())) {
            return null;
        }

        $definedVariablesHash = $companion->getDefinedVariablesHash();

        // If the file exists and seems legit, keep the file.
        if (file_exists($filename = $this->rootDirectory.DIRECTORY_SEPARATOR.$variable->getValue()) && isset($definedVariablesHash[$variable->getName()])) {
            return $definedVariablesHash[$variable->getName()];
        }

        $downloadedFilePath = $companion->ask('<comment>'.$variable->getName().'</comment>: What is the path of your downloaded file? ');
        if (!file_exists($downloadedFilePath)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist', $downloadedFilePath));
        }

        if (false === file_put_contents($filename, file_get_contents($downloadedFilePath))) {
            throw new \RuntimeException(sprintf(
                'Unable to write into "%s"',
                $filename
            ));
        }

        return $variable->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function isVariableRequiringValue(Companion $companion, Block $block, Variable $variable, string $currentValue = null)
    {
        if (null === ($attribute = $block->getAttribute('file-to-propagate')) || !in_array($variable->getName(), $attribute->getVariableNames())) {
            return false;
        }

        return !file_exists($this->rootDirectory.DIRECTORY_SEPARATOR.$variable->getValue());
    }
}
