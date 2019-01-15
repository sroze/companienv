<?php

namespace Companienv\DotEnv;

use Jackiedo\DotenvEditor\DotenvFormatter;

class ValueFormatter extends DotenvFormatter
{
    public function formatValue($value, $forceQuotes = false)
    {
        if (!$forceQuotes) {
            return $value;
        }

        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace('"', '\"', $value);
        $value = "\"{$value}\"";

        return $value;
    }
}
