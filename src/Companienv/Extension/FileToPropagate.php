<?php

namespace Companienv\Extension;

use Companienv\Companion;
use Companienv\DotEnv\Block;
use Companienv\DotEnv\Variable;
use Companienv\Extension;

class FileToPropagate implements Extension
{
    /**
     * {@inheritdoc}
     */
    public function getVariableValue(Companion $companion, Block $block, Variable $variable)
    {
        if (null === ($attribute = $block->getAttribute('file-to-propagate', $variable))) {
            return null;
        }

        $definedVariablesHash = $companion->getDefinedVariablesHash();
        $fileSystem = $companion->getFileSystem();

        // If the file exists and seems legit, keep the file.
        if ($fileSystem->exists($filename = $variable->getValue()) && isset($definedVariablesHash[$variable->getName()])) {
            return $definedVariablesHash[$variable->getName()];
        }

        $downloadedFilePath = $companion->ask('<comment>'.$variable->getName().'</comment>: What is the path of your downloaded file? ');
        if (!$fileSystem->exists($downloadedFilePath)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist', $downloadedFilePath));
        }

        if (false === $fileSystem->write($filename, $fileSystem->getContents($downloadedFilePath))) {
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
    public function isVariableRequiringValue(Companion $companion, Block $block, Variable $variable, string $currentValue = null) : int
    {
        if (null === ($attribute = $block->getAttribute('file-to-propagate', $variable))) {
            return Extension::ABSTAIN;
        }

        return $companion->getFileSystem()->exists($variable->getValue())
             ? Extension::VARIABLE_REQUIRED
             : Extension::ABSTAIN;
    }
}
