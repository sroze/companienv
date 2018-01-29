<?php

namespace Companienv\DotEnv;

class Attribute
{
    private $name;
    private $variableNames;

    /**
     * @param string   $name
     * @param string[] $variableNames
     */
    public function __construct(string $name, array $variableNames)
    {
        $this->name = $name;
        $this->variableNames = $variableNames;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVariableNames(): array
    {
        return $this->variableNames;
    }
}
