<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class ParserSyntaxErrorTest extends TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    public function setUp() : void
    {
        $this->parser = new Parser(new Lexer(), new Parser\ExpanderInitializer(new Backtrace()));
    }

    public function test_unexpected_statement_at_type_position()
    {
        $this->expectException(\Coduo\PHPMatcher\Exception\PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 0: Error: Expected "@type@ pattern", got "not"');

        $this->parser->getAST('not_valid_type');
    }

    public function test_unexpected_statement_instead_of_expander()
    {
        $this->expectException(\Coduo\PHPMatcher\Exception\PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 6: Error: Expected ".expanderName(args) definition", got "anything"');

        $this->parser->getAST('@type@anything');
    }

    public function test_end_of_string_after_opening_parenthesis()
    {
        $this->expectException(\Coduo\PHPMatcher\Exception\PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 14: Error: Expected ")", got end of string.end of string');

        $this->parser->getAST('@type@.expander(');
    }

    public function test_not_argument_after_opening_parenthesis()
    {
        $this->expectException(\Coduo\PHPMatcher\Exception\PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 16: Error: Expected "string, number, boolean or null argument", got "not"');

        $this->parser->getAST('@type@.expander(not_argument');
    }

    public function test_missing_close_parenthesis_after_single_argument()
    {
        $this->expectException(\Coduo\PHPMatcher\Exception\PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 22: Error: Expected ")", got end of string.end of string');

        $this->parser->getAST("@type@.expander('string'");
    }

    public function test_missing_close_parenthesis_after_multiple_arguments()
    {
        $this->expectException(\Coduo\PHPMatcher\Exception\PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 26: Error: Expected ")", got end of string.end of string');

        $this->parser->getAST("@type@.expander('string',1");
    }

    public function test_missing_argument_after_comma()
    {
        $this->expectException(\Coduo\PHPMatcher\Exception\PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 25: Error: Expected "string, number, boolean or null argument", got ")"');

        $this->parser->getAST("@type@.expander('string',)");
    }

    public function test_not_argument_after_comma()
    {
        $this->expectException(\Coduo\PHPMatcher\Exception\PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 25: Error: Expected "string, number, boolean or null argument", got "not"');

        $this->parser->getAST("@type@.expander('string',not_argument");
    }
}
