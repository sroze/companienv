<?php

namespace Companienv\DotEnv;

use Companienv\IO\FileSystem\FileSystem;

class Parser
{
    public function parse(FileSystem $fileSystem, string $path) : File
    {
        $blocks = [];

        /** @var Block|null $block */
        $block = null;
        foreach (explode("\n", $fileSystem->getContents($path)) as $number => $line) {
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
                        $block->addAttribute($this->parseAttribute(substr($line, 2)));
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

    private function parseAttribute(string $string)
    {
        $variableNameRegex = '[A-Z0-9_]+';
        $valueRegex = '[^\) ]+';

        if (!preg_match('/^([a-z0-9-]+)\((('.$variableNameRegex.' ?)*)\)(:\((('.$variableNameRegex.'='.$valueRegex.' ?)*)\))?$/', $string, $matches)) {
            throw new \RuntimeException(sprintf(
                'Unable to parse the given attribute: %s',
                $string
            ));
        }

        return new Attribute($matches[1], explode(' ', $matches[2]), isset($matches[6]) ? $this->dotEnvMappingToKeyBasedMapping($matches[6]) : []);
    }

    private function dotEnvMappingToKeyBasedMapping(string $dotEnvMapping)
    {
        $mapping = [];
        $envMappings = explode(' ', $dotEnvMapping);

        foreach ($envMappings as $envMapping) {
            if (false === strpos($envMapping, '=')) {
                throw new \RuntimeException(sprintf(
                    'Could not parse attribute mapping "%s"',
                    $dotEnvMapping
                ));
            }

            list($key, $value) = explode('=', $envMapping);
            $mapping[$key] = $value;
        }

        return $mapping;
    }
}
