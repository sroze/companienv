<?php

namespace Companienv;

use Companienv\DotEnv\Block;
use Companienv\DotEnv\Variable;

interface Extension
{
    /**
     * Get the variable value, from a given source.
     *
     * Return `null` if was unable to get the value.
     *
     * @param Companion $companion
     * @param Block $block
     * @param Variable $variable
     *
     * @return string|null
     */
    public function getVariableValue(Companion $companion, Block $block, Variable $variable);
}
