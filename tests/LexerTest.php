<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Lexer;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    public static function validStringValuesProvider()
    {
        return [
            ['"String"'],
            ["'String'"],
        ];
    }

    public static function validNumberValuesProvider()
    {
        return [
            ['125', 125],
            ['12.15', 12.15],
            ['-10', -10],
            ['-1.24', -1.24],
        ];
    }

    public static function validBooleanValuesProvider()
    {
        return [
            ['true', true],
            ['false', false],
            ['TRUE', true],
            ['fAlSe', false],
        ];
    }

    public static function validNullValuesProvider()
    {
        return [
            ['null'],
            ['NULL'],
            ['NuLl'],
        ];
    }

    public static function validNonTokenValuesProvider()
    {
        return [
            ['@integer'],
            ['integer@'],
            ['test'],
            ['@'],
        ];
    }

    public static function validMatcherTypePatterns()
    {
        return [
            ['@string@'],
            ['@boolean@'],
            ['@integer@'],
            ['@number@'],
            ['@*@'],
            ['@...@'],
            ['@wildcard@'],
        ];
    }

    public static function validExpanderNamesProvider()
    {
        return [
            ['expanderName(', 'expanderName'],
            ['e(', 'e'],
            ['.e(', 'e'],
        ];
    }

    /**
     * @dataProvider validStringValuesProvider
     */
    public function test_string_values(string $value) : void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_STRING);
        $this->assertEquals($lexer->lookahead['value'], \trim(\trim($value, "'"), '"'));
    }

    /**
     * @dataProvider validNumberValuesProvider
     */
    public function test_number_values($value, $expectedValue) : void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_NUMBER);
        $this->assertEquals($expectedValue, $lexer->lookahead['value']);
    }

    /**
     * @dataProvider validBooleanValuesProvider
     */
    public function test_boolean_values($value, $expectedValue) : void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_BOOLEAN);
        $this->assertEquals($lexer->lookahead['value'], $expectedValue);
    }

    /**
     * @dataProvider validNullValuesProvider
     */
    public function test_null_values($value) : void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_NULL);
        $this->assertNull($lexer->lookahead['value']);
    }

    /**
     * @dataProvider validNonTokenValuesProvider
     */
    public function test_non_token_values($value) : void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_NONE);
    }

    public function test_close_parenthesis() : void
    {
        $lexer = new Lexer();
        $lexer->setInput(')');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_CLOSE_PARENTHESIS);
    }

    public function test_close_open_brace() : void
    {
        $lexer = new Lexer();
        $lexer->setInput('{');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_OPEN_CURLY_BRACE);
    }

    public function test_close_curly_brace() : void
    {
        $lexer = new Lexer();
        $lexer->setInput('}');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_CLOSE_CURLY_BRACE);
    }

    public function test_colon() : void
    {
        $lexer = new Lexer();
        $lexer->setInput(':');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_COLON);
    }

    public function test_comma() : void
    {
        $lexer = new Lexer();
        $lexer->setInput(',');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_COMMA);
    }

    /**
     * @dataProvider validMatcherTypePatterns
     */
    public function test_type_pattern($value) : void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_TYPE_PATTERN);
        $this->assertEquals($lexer->lookahead['value'], \trim($value, '@'));
    }

    /**
     * @dataProvider validExpanderNamesProvider
     */
    public function test_expander_name($value, $expectedTokenValue) : void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_EXPANDER_NAME);
        $this->assertEquals($lexer->lookahead['value'], $expectedTokenValue);
    }

    public function test_ignore_whitespaces_between_parenthesis() : void
    {
        $expectedTokens = ['type', 'expander', 'arg1', ',', 2, ',', 'arg3', ',', 4, ')'];
        $lexer = new Lexer();
        $lexer->setInput("@type@.expander( 'arg1',    2    ,'arg3',4)");

        $this->assertEquals($expectedTokens, $this->collectTokens($lexer));
    }

    /**
     * @param $lexer
     *
     * @return array
     */
    protected function collectTokens(Lexer $lexer)
    {
        $tokens = [];

        while ($lexer->moveNext()) {
            $tokens[] = $lexer->lookahead['value'];
        }

        return $tokens;
    }
}
