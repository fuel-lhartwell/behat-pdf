Feature: Pdf export

  Scenario: PDF must contain text
    Given I have pdf located at "samples/sample1.pdf"
    When I parse the pdf content
    Then the the page count should be "1"
    Then page "1" should contain
    """
Document title  Calibri : Lorem ipsum dolor sit amet, consectetur adipiscing elit.
    """