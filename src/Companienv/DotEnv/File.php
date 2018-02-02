<?php

namespace Companienv\DotEnv;

class File
{
    private $header;
    private $blocks;

    public function __construct(string $header = '', array $blocks = [])
    {
        $this->header = $header;
        $this->blocks = $blocks;
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @return Variable[]
     */
    public function getAllVariables() : array
    {
        return array_reduce($this->blocks, function (array $carry, Block $block) {
            return array_merge($carry, $block->getVariables());
        }, []);
    }
}
