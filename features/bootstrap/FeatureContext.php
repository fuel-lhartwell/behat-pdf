<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Parser;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{

    /**
     * The PDF filename, relative to the root directory
     * @var string
     */
    protected $filename;

    /**
     * The Extracted PDF metadata
     * @var array
     *
     * Example :
     * (
     *    [Author] =>
     *    [CreationDate] => 2013-09-01T21:56:33+02:00
     *    [Creator] =>
     *    [Keywords] =>
     *    [ModDate] => 2013-09-01T21:56:33+02:00
     *    [Producer] => Foxit Reader PDF Printer Version 6.0.3.0513
     *    [Subject] => subject
     *    [Title] => title
     *    [Pages] => 1
     * )
     *
     */
    protected $metadata;

    /**
     * The PDF content
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
     * @param string $filename
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
        $this->metadata = $pdf->getDetails();

        foreach ($pages as $i => $page) {
            $this->pages[++$i] = $page->getText();
        }
    }

    /**
     * @Then page :pageNum should contain
     * @param int $pageNum
     * @param PyStringNode $string
     */
    public function pageShouldContain($pageNum, PyStringNode $string)
    {
        PHPUnit_Framework_Assert::assertContains((string) $string, $this->pages[$pageNum]);
    }

    /**
     * @Then the the page count should be :pageCount
     * @param int $pageCount
     */
    public function theThePageCountShouldBe($pageCount)
    {
        PHPUnit_Framework_Assert::assertEquals( (int) $pageCount, $this->metadata['Pages']);
    }

}