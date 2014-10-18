<?php

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Parser;

class ParserSyntaxErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    public function setUp()
    {
        $this->parser = new Parser(new Lexer(), new Parser\ExpanderInitializer());
    }

    /**
     * @expectedException \Coduo\PHPMatcher\Exception\PatternException
     * @expectedExceptionMessage [Syntax Error] line 0, col 0: Error: Expected "@type@ pattern", got "not"
     */
    public function test_unexpected_statement_at_type_position()
    {
        $this->parser->getAST("not_valid_type");
    }

    /**
     * @expectedException \Coduo\PHPMatcher\Exception\PatternException
     * @expectedExceptionMessage [Syntax Error] line 0, col 6: Error: Expected ".expanderName(args) definition", got "anything"
     */
    public function test_unexpected_statement_instead_of_expander()
    {
        $this->parser->getAST("@type@anything");
    }

    /**
     * @expectedException \Coduo\PHPMatcher\Exception\PatternException
     * @expectedExceptionMessage [Syntax Error] line 0, col 14: Error: Expected ")", got end of string.end of string
     */
    public function test_end_of_string_after_opening_parenthesis()
    {
        $this->parser->getAST("@type@.expander(");
    }

    /**
     * @expectedException \Coduo\PHPMatcher\Exception\PatternException
     * @expectedExceptionMessage [Syntax Error] line 0, col 16: Error: Expected "string, number, boolean or null argument", got "not"
     */
    public function test_not_argument_after_opening_parenthesis()
    {
        $this->parser->getAST("@type@.expander(not_argument");
    }

    /**
     * @expectedException \Coduo\PHPMatcher\Exception\PatternException
     * @expectedExceptionMessage [Syntax Error] line 0, col 22: Error: Expected ")", got end of string.end of string
     */
    public function test_missing_close_parenthesis_after_single_argument()
    {
        $this->parser->getAST("@type@.expander('string'");
    }

    /**
     * @expectedException \Coduo\PHPMatcher\Exception\PatternException
     * @expectedExceptionMessage [Syntax Error] line 0, col 26: Error: Expected ")", got end of string.end of string
     */
    public function test_missing_close_parenthesis_after_multiple_arguments()
    {
        $this->parser->getAST("@type@.expander('string',1");
    }

    /**
     * @expectedException \Coduo\PHPMatcher\Exception\PatternException
     * @expectedExceptionMessage [Syntax Error] line 0, col 25: Error: Expected "string, number, boolean or null argument", got ")"
     */
    public function test_missing_argument_after_comma()
    {
        $this->parser->getAST("@type@.expander('string',)");
    }

    /**
     * @expectedException \Coduo\PHPMatcher\Exception\PatternException
     * @expectedExceptionMessage [Syntax Error] line 0, col 25: Error: Expected "string, number, boolean or null argument", got "not"
     */
    public function test_not_argument_after_comma()
    {
        $this->parser->getAST("@type@.expander('string',not_argument");
    }
}
