<?php

namespace Companienv\Extension;

use Companienv\Companion;
use Companienv\DotEnv\Block;
use Companienv\DotEnv\Variable;
use Companienv\Extension;

class Chained implements Extension
{
    /**
     * @var array|Extension[]
     */
    private $extensions;

    /**
     * @param Extension[] $extensions
     */
    public function __construct(array $extensions = [])
    {
        $this->extensions = $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariableValue(Companion $companion, Block $block, Variable $variable)
    {
        foreach ($this->extensions as $extension) {
            if (null !== ($value = $extension->getVariableValue($companion, $block, $variable))) {
                return $value;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isVariableRequiringValue(Companion $companion, Block $block, Variable $variable, string $currentValue = null) : int
    {
        foreach ($this->extensions as $extension) {
            if (($vote = $extension->isVariableRequiringValue($companion, $block, $variable, $currentValue)) != Extension::ABSTAIN) {
                return $vote;
            }
        }

        return Extension::ABSTAIN;
    }
}
