<?php

namespace Companienv\Extension;

use Companienv\Companion;
use Companienv\DotEnv\Block;
use Companienv\DotEnv\Variable;
use Companienv\Extension;

/**
 * An abstract class that contains default non-intrusive implementations of the extension methods.
 *
 */
class AbstractExtension implements Extension
{
    /**
     * {@inheritdoc}
     */
    public function getVariableValue(Companion $companion, Block $block, Variable $variable)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isVariableRequiringValue(Companion $companion, Block $block, Variable $variable, string $currentValue = null) : int
    {
        return Extension::ABSTAIN;
    }
}
