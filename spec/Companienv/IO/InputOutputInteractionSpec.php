<?php

namespace spec\Companienv\IO;

use Companienv\IO\InputOutputInteraction;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InputOutputInteractionSpec extends ObjectBehavior
{
    function let(InputInterface $input, OutputInterface $output)
    {
        $this->beConstructedWith($input, $output);
    }

    function it_will_return_the_default_value()
    {
        $this->ask('VALUE ?', 'true')->shouldReturn('true');
    }

    function it_will_return_the_default_value_even_if_it_looks_falsy()
    {
        $this->ask('COUNT ?', '0')->shouldReturn('0');
    }
}
