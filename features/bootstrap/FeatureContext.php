<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Parser;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var Page[]
     */
    protected $pages;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given I have pdf located at :filename
     */
    public function iHavePdfLocatedAt($filename)
    {
        if (!is_readable($filename)) {
            Throw new \InvalidArgumentException(sprintf('The file [%s] is not readable', $filename));
        }

        $this->filename = $filename;
    }

    /**
     * @When I parse the pdf content
     */
    public function iParseThePdfContent()
    {
        $parser = new Parser();
        $pdf    = $parser->parseFile($this->filename);
        $pages  = $pdf->getPages();

        foreach ($pages as $i => $page) {
            $this->pages[++$i] = $page->getText();
        }
    }

    /**
     * @Then page :pageNum should contain
     */
    public function pageShouldContain($pageNum, PyStringNode $string)
    {
        PHPUnit_Framework_Assert::assertContains((string) $string, $this->pages[$pageNum]);
    }

}
