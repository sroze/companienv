<?php

namespace Companienv\DotEnv;

class Parser
{
    public function parse(string $path) : File
    {
        $blocks = [];

        /** @var Block|null $block */
        $block = null;
        foreach (file($path) as $number => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (strpos($line, '#') === 0) {
                // We see a title
                if (substr($line, 0, 2) == '##') {
                    $block = new Block(trim($line, '# '));
                    $blocks[] = $block;
                } elseif (substr($line, 0, 2) == '#~') {
                    // Ignore this comment.
                } elseif ($block !== null) {
                    if (substr($line, 0, 2) == '#+') {
                        $block->addAttribute(substr($line, 2));
                    } else {
                        $block->appendToDescription(trim($line, '# '));
                    }
                }
            } else {
                // This is a variable
                $sides = explode('=', $line);
                if (count($sides) != 2) {
                    throw new \InvalidArgumentException(sprintf(
                        'The line %d of the file %s is invalid: %s',
                        $number,
                        $path,
                        $line
                    ));
                }

                $block->addVariable(new Variable($sides[0], $sides[1]));
            }
        }

        return new File('', $blocks);
    }
}
