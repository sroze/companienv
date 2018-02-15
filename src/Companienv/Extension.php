<?php

namespace Companienv;

use Companienv\DotEnv\Block;
use Companienv\DotEnv\Variable;

interface Extension
{
    const VARIABLE_REQUIRED = 1;
    const VARIABLE_SKIP = -1;
    const ABSTAIN = 0;

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

    /**
     * Is this variable requiring a new value?
     *
     * The return value is one of the ABSTAINT, VARIABLE_REQUIRED or VARIABLE_SKIP constants.
     *
     * @param Companion $companion
     * @param Block $block
     * @param Variable $variable
     * @param string|null $currentValue
     *
     * @return int
     */
    public function isVariableRequiringValue(Companion $companion, Block $block, Variable $variable, string $currentValue = null) : int;
}
