<?php

namespace spec\Companienv\IO;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InputOutputInteractionSpec extends ObjectBehavior
{
    function let(InputInterface $input, OutputInterface $output)
    {
        $this->beConstructedWith($input, $output);
    }
}
