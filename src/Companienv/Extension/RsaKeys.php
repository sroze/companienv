<?php

namespace Companienv\Extension;

use Companienv\Companion;
use Companienv\DotEnv\Block;
use Companienv\DotEnv\Variable;
use Companienv\Extension;
use Symfony\Component\Process\Process;

class RsaKeys implements Extension
{
    private $populatedVariables = [];

    /**
     * {@inheritdoc}
     */
    public function getVariableValue(Companion $companion, Block $block, Variable $variable)
    {
        if (null === ($attribute = $block->getAttribute('rsa-pair', $variable))) {
            return null;
        }

        if (isset($this->populatedVariables[$variable->getName()])) {
            return $this->populatedVariables[$variable->getName()];
        }

        if (!$companion->askConfirmation(sprintf(
            'Variables %s represents an RSA public/private key. Do you want to automatically generate them? (y) ',
            implode(' and ', array_map(function ($variable) {
                return '<comment>'.$variable.'</comment>';
            }, $attribute->getVariableNames()))
        ))) {
            // Ensure we don't ask anymore for this variable pair
            foreach ($attribute->getVariableNames() as $variableName) {
                $this->populatedVariables[$variableName] = null;
            }

            return null;
        }

        $fileSystem = $companion->getFileSystem();
        $passPhrase = $companion->ask('Enter pass phrase to protect the keys: ');
        $privateKeyPath = $block->getVariable($privateKeyVariableName = $attribute->getVariableNames()[0])->getValue();
        $publicKeyPath = $block->getVariable($publicKeyVariableName = $attribute->getVariableNames()[1])->getValue();

        try {
            (new Process(['openssl', 'genrsa', '-out', $fileSystem->realpath($privateKeyPath), '-aes256', '-passout', 'pass:' . $passPhrase, '4096']))->mustRun();
            (new Process(['openssl', 'rsa', '-pubout', '-in', $fileSystem->realpath($privateKeyPath), '-out', $fileSystem->realpath($publicKeyPath), '-passin', 'pass:' . $passPhrase]))->mustRun();
        } catch (\Symfony\Component\Process\Exception\RuntimeException $e) {
            throw new \RuntimeException('Could not have generated the RSA public/private key', $e->getCode(), $e);
        }

        $this->populatedVariables[$privateKeyVariableName] = $privateKeyPath;
        $this->populatedVariables[$publicKeyVariableName] = $publicKeyPath;
        $this->populatedVariables[$attribute->getVariableNames()[2]] = $passPhrase;

        return $this->populatedVariables[$variable->getName()];
    }

    /**
     * {@inheritdoc}
     */
    public function isVariableRequiringValue(Companion $companion, Block $block, Variable $variable, string $currentValue = null) : int
    {
        if (null === ($attribute = $block->getAttribute('rsa-pair', $variable))) {
            return Extension::ABSTAIN;
        }

        $fileSystem = $companion->getFileSystem();

        return (
            !$fileSystem->exists($block->getVariable($attribute->getVariableNames()[0])->getValue())
            || !$fileSystem->exists($block->getVariable($attribute->getVariableNames()[1])->getValue())
        ) ? Extension::VARIABLE_REQUIRED
          : Extension::ABSTAIN;
    }
}
