<?php

namespace Companienv\DotEnv;

class Attribute
{
    private $name;
    private $variableNames;
    private $labels;

    /**
     * @param string $name
     * @param string[] $variableNames
     * @param string[] $labels        String key associated array of string
     */
    public function __construct(string $name, array $variableNames, array $labels)
    {
        $this->name = $name;
        $this->variableNames = $variableNames;
        $this->labels = $labels;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVariableNames(): array
    {
        return $this->variableNames;
    }

    public function getLabels()
    {
        return $this->labels;
    }
}
