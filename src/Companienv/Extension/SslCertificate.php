<?php

namespace Companienv\Extension;

use Companienv\Companion;
use Companienv\DotEnv\Block;
use Companienv\DotEnv\Variable;
use Companienv\Extension;
use Symfony\Component\Process\Process;

class SslCertificate implements Extension
{
    private $populatedVariables = [];
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
        if (null === ($attribute = $block->getAttribute('ssl-certificate')) || !in_array($variable->getName(), $attribute->getVariableNames())) {
            return null;
        }

        if (isset($this->populatedVariables[$variable->getName()])) {
            return $this->populatedVariables[$variable->getName()];
        }

        if (!$companion->askConfirmation(sprintf(
            'Variables %s represents an SSL certificate. Do you want to automatically generate them? (y) ',
            implode(' and ', array_map(function($variable) {
                return '<comment>'.$variable.'</comment>';
            }, $attribute->getVariableNames()))
        ))) {
            // Ensure we don't ask anymore for this variable pair
            foreach ($attribute->getVariableNames() as $variable) {
                $this->populatedVariables[$variable] = null;
            }

            return null;
        }

        $domainName = $companion->ask('Enter the domain name for which to generate the self-signed SSL certificate: ');
        $privateKeyPath = $block->getVariable($privateKeyVariableName = $attribute->getVariableNames()[0])->getValue();
        $certificateKeyPath = $block->getVariable($certificateVariableName = $attribute->getVariableNames()[1])->getValue();

        try {
            (new Process(sprintf(
                'openssl req -x509 -nodes -days 3650 -newkey rsa:2048 -keyout %s -out %s -subj "/C=SS/ST=SS/L=SelfSignedCity/O=SelfSignedOrg/CN=%s"',
                $this->realpath($privateKeyPath),
                $this->realpath($certificateKeyPath),
                $domainName
            )))->mustRun();
        } catch (\Symfony\Component\Process\Exception\RuntimeException $e) {
            throw new \RuntimeException('Could not have generated the SSL certificate: '.$e->getMessage(), $e->getCode(), $e);
        }

        $this->populatedVariables[$privateKeyVariableName] = $privateKeyPath;
        $this->populatedVariables[$certificateVariableName] = $certificateKeyPath;
        $this->populatedVariables[$attribute->getVariableNames()[2]] = $domainName;

        return $this->populatedVariables[$variable->getName()];
    }

    /**
     * {@inheritdoc}
     */
    public function isVariableRequiringValue(Companion $companion, Block $block, Variable $variable, string $currentValue = null)
    {
        if (null === ($attribute = $block->getAttribute('ssl-certificate')) || !in_array($variable->getName(), $attribute->getVariableNames())) {
            return false;
        }

        $privateKeyPath = $this->realpath($block->getVariable($privateKeyVariableName = $attribute->getVariableNames()[0])->getValue());
        $certificateKeyPath = $this->realpath($block->getVariable($attribute->getVariableNames()[1])->getValue());

        return !file_exists($privateKeyPath) || !file_exists($certificateKeyPath);
    }

    private function realpath($path)
    {
        return $this->rootDirectory.DIRECTORY_SEPARATOR.$path;
    }
}
