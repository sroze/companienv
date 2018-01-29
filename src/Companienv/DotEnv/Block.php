<?php

namespace Companienv\DotEnv;

class Block
{
    private $title;
    private $description;

    /** @var Variable[] */
    private $variables;

    /** @var Attribute[] */
    private $attributes;

    public function __construct(string $title, string $description = '', array $variables = [], array $attributes = [])
    {
        $this->title = $title;
        $this->description = $description;
        $this->variables = $variables;
        $this->attributes = $attributes;
    }

    public function appendToDescription(string $string)
    {
        $this->description .= $string;
    }

    public function addVariable(Variable $variable)
    {
        $this->variables[] = $variable;
    }

    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return Variable[]
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getVariablesInBlock(array $variables)
    {
        $blockVariableNames = array_map(function (Variable $variable) {
            return $variable->getName();
        }, $this->variables);

        return array_filter($variables, function (Variable $variable) use ($blockVariableNames) {
            return in_array($variable->getName(), $blockVariableNames);
        });
    }

    /**
     * @param string $name
     *
     * @return Attribute|null
     */
    public function getAttribute(string $name)
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() == $name) {
                return $attribute;
            }
        }

        return null;
    }
}
