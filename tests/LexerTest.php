<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Lexer;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    /**
     * @dataProvider validStringValuesProvider
     */
    public function test_string_values(string $value)
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_STRING);
        $this->assertEquals($lexer->lookahead['value'], \trim(\trim($value, "'"), '"'));
    }

    public static function validStringValuesProvider()
    {
        return [
            ['"String"'],
            ["'String'"],
        ];
    }


    /**
     * @dataProvider validNumberValuesProvider
     */
    public function test_number_values($value, $expectedValue)
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_NUMBER);
        $this->assertEquals($expectedValue, $lexer->lookahead['value']);
    }

    public static function validNumberValuesProvider()
    {
        return [
            [1, 1],
            [1.25, 1.25],
            [0, 0],
            ['125', 125],
            ['12.15', 12.15],
            [-10, -10],
            [-1.124, -1.124],
            ['-10', -10],
            ['-1.24', -1.24]
        ];
    }

    /**
     * @dataProvider validBooleanValuesProvider
     */
    public function test_boolean_values($value, $expectedValue)
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_BOOLEAN);
        $this->assertEquals($lexer->lookahead['value'], $expectedValue);
    }

    public static function validBooleanValuesProvider()
    {
        return [
            ['true', true],
            ['false', false],
            ['TRUE', true],
            ['fAlSe', false]
        ];
    }

    /**
     * @dataProvider validNullValuesProvider
     */
    public function test_null_values($value)
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_NULL);
        $this->assertNull($lexer->lookahead['value']);
    }

    public static function validNullValuesProvider()
    {
        return [
            ['null'],
            ['NULL'],
            ['NuLl'],
        ];
    }

    /**
     * @dataProvider validNonTokenValuesProvider
     */
    public function test_non_token_values($value)
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_NONE);
    }

    public static function validNonTokenValuesProvider()
    {
        return [
            ['@integer'],
            ['integer@'],
            ['test'],
            ['@']
        ];
    }

    public function test_close_parenthesis()
    {
        $lexer = new Lexer();
        $lexer->setInput(')');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_CLOSE_PARENTHESIS);
    }

    public function test_close_open_brace()
    {
        $lexer = new Lexer();
        $lexer->setInput('{');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_OPEN_CURLY_BRACE);
    }

    public function test_close_curly_brace()
    {
        $lexer = new Lexer();
        $lexer->setInput('}');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_CLOSE_CURLY_BRACE);
    }

    public function test_colon()
    {
        $lexer = new Lexer();
        $lexer->setInput(':');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_COLON);
    }

    public function test_comma()
    {
        $lexer = new Lexer();
        $lexer->setInput(',');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_COMMA);
    }

    /**
     * @dataProvider validMatcherTypePatterns
     */
    public function test_type_pattern($value)
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_TYPE_PATTERN);
        $this->assertEquals($lexer->lookahead['value'], \trim($value, '@'));
    }

    public static function validMatcherTypePatterns()
    {
        return [
            ['@string@'],
            ['@boolean@'],
            ['@integer@'],
            ['@number@'],
            ['@*@'],
            ['@wildcard@']
        ];
    }

    /**
     * @dataProvider validExpanderNamesProvider
     */
    public function test_expander_name($value, $expectedTokenValue)
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_EXPANDER_NAME);
        $this->assertEquals($lexer->lookahead['value'], $expectedTokenValue);
    }

    public static function validExpanderNamesProvider()
    {
        return [
            ['expanderName(', 'expanderName'],
            ['e(', 'e'],
            ['.e(', 'e']
        ];
    }

    public function test_ignore_whitespaces_between_parenthesis()
    {
        $expectedTokens = ['type', 'expander', 'arg1', ',', 2, ',', 'arg3', ',', 4, ')'];
        $lexer = new Lexer();
        $lexer->setInput("@type@.expander( 'arg1',    2    ,'arg3',4)");

        $this->assertEquals($expectedTokens, $this->collectTokens($lexer));
    }

    /**
     * @param $lexer
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
