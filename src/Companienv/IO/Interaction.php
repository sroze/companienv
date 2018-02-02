<?php

namespace Companienv\IO;

interface Interaction
{
    public function askConfirmation(string $question) : bool;

    public function ask(string $question, string $default = null) : string;

    public function writeln($messageOrMessages);
}
