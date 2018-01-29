<?php

namespace Companienv\Interaction;

use Companienv\Companion;
use Companienv\DotEnv\Block;
use Companienv\DotEnv\Variable;
use Companienv\Extension;

class AskVariableValues implements Extension
{
    public function getVariableValue(Companion $companion, Block $block, Variable $variable)
    {
        $definedVariablesHash = $companion->getDefinedVariablesHash();
        $defaultValue = isset($definedVariablesHash[$variable->getName()]) ? $definedVariablesHash[$variable->getName()] : $variable->getValue();
        $question = sprintf('<comment>%s</comment> ? ', $variable->getName());

        if ($defaultValue) {
            $question .= '('.$defaultValue.') ';
        }

        return $companion->ask($question, $defaultValue);
    }
}
