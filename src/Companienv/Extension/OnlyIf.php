<?php

namespace Companienv\Extension;

use Companienv\Companion;
use Companienv\DotEnv\Attribute;
use Companienv\DotEnv\Block;
use Companienv\DotEnv\Variable;
use Companienv\Extension;

class OnlyIf implements Extension
{
    /**
     * {@inheritdoc}
     */
    public function getVariableValue(Companion $companion, Block $block, Variable $variable)
    {
        if (null === ($attribute = $block->getAttribute('only-if', $variable))) {
            return null;
        }

        if (!$this->matchesCondition($companion, $attribute)) {
            return $variable->getValue();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isVariableRequiringValue(Companion $companion, Block $block, Variable $variable, string $currentValue = null)
    {
        if (null === ($attribute = $block->getAttribute('only-if', $variable))) {
            return false;
        }

        return $this->matchesCondition($companion, $attribute);
    }

    private function matchesCondition(Companion $companion, Attribute $attribute) : bool
    {
        $definedVariablesHash = $companion->getDefinedVariablesHash();
        foreach ($attribute->getLabels() as $otherVariableName => $expectedValue) {
            if (!isset($definedVariablesHash[$otherVariableName]) || $definedVariablesHash[$otherVariableName] != $expectedValue) {
                return false;
            }
        }

        return true;
    }
}
