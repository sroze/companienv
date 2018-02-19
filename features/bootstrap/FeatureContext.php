<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Companienv\Application;
use Companienv\Companion;
use Companienv\Extension\Chained;
use Companienv\IO\InMemoryFileSystem;
use Companienv\IO\InMemoryInteraction;
use Symfony\Component\Process\Process;

class FeatureContext implements Context
{
    private $interaction;
    private $fileSystem;
    private $companion;

    public function __construct()
    {
        $this->fileSystem = new InMemoryFileSystem();
    }

    /**
     * @Given the file :path contains:
     */
    public function theFileContains($path, PyStringNode $string)
    {
        $this->fileSystem->write($path, $string->getRaw());
    }

    /**
     * @When I run the companion with the following answers:
     * @When I run the companion
     */
    public function iRunTheCompanionWithTheFollowingAnswers(TableNode $table = null)
    {
        $this->companion = new Companion(
            $this->fileSystem,
            $this->interaction = new InMemoryInteraction($table !== null ? $table->getRowsHash() : []),
            new Chained(Application::defaultExtensions())
        );

        $this->companion->fillGaps();

        echo $this->interaction->getBuffer();
    }

    /**
     * @Then the file :path should contain:
     */
    public function theFileShouldContain($path, PyStringNode $string)
    {
        $found = trim($this->fileSystem->getContents($path));
        $expected = trim($string->getRaw());

        if ($found != $expected) {
            throw new \RuntimeException(sprintf(
                'Found following instead: %s',
                $found
            ));
        }
    }

    /**
     * @Then the companion's output will look like that:
     */
    public function theCompanionsOutputWillLookLikeThat(PyStringNode $string)
    {
        $found = strip_tags(trim($this->interaction->getBuffer()));
        $expected = trim($string->getRaw());

        if ($found != $expected) {
            throw new \RuntimeException(sprintf(
                'Found the following instead: %s',
                function_exists('xdiff_string_diff') ? xdiff_string_diff($expected, $found) : $found
            ));
        }
    }

    /**
     * @Then the companion's output should be empty
     */
    public function theCompanionsOutputShouldBeEmpty()
    {
        $found = strip_tags(trim($this->interaction->getBuffer()));

        if (!empty($found)) {
            throw new \RuntimeException(sprintf(
                'Found the following instead: %s',
                $found
            ));
        }
    }
}
