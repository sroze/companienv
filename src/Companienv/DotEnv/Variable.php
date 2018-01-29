<?php

namespace Companienv\DotEnv;

class Variable
{
    private $name;
    private $value;

    public function __construct(string $name, string $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasValue(): bool
    {
        return $this->value !== null;
    }

    public function getValue()
    {
        return $this->value;
    }
}
