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
     */
    public function iRunTheCompanionWithTheFollowingAnswers(TableNode $table)
    {
        $this->companion = new Companion(
            $this->fileSystem,
            $interaction = new InMemoryInteraction($table->getRowsHash()),
            new Chained(Application::defaultExtensions())
        );

        $this->companion->fillGaps();

        echo $interaction->getBuffer();
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
}
