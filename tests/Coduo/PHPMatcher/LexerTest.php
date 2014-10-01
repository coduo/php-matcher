<?php

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Lexer;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider validStringValuesProvider
     */
    public function test_string_values($value)
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead['type'], Lexer::T_STRING);
        $this->assertEquals($lexer->lookahead['value'], trim(trim($value, "'"), '"'));
    }

    public static function validStringValuesProvider()
    {
        return array(
            array('"String"'),
            array("'String'"),
        );
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
        return array(
            array(1, 1),
            array(1.25, 1.25),
            array(0, 0),
            array("125", 125),
            array("12.15", 12.15),
            array(-10, -10),
            array(-1.124, -1.124),
            array("-10", -10),
            array("-1.24", -1.24)
        );
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
        return array(
            array("true", true),
            array("false", false),
            array("TRUE", true),
            array("fAlSe", false)
        );
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
        return array(
            array("null"),
            array("NULL"),
            array("NuLl"),
        );
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
        return array(
            array("@integer"),
            array("integer@"),
            array("test"),
            array("@")
        );
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
        $this->assertEquals($lexer->lookahead['value'], trim($value, "@"));
    }

    public static function validMatcherTypePatterns()
    {
        return array(
            array("@string@"),
            array("@boolean@"),
            array("@integer@"),
            array("@number@"),
            array("@*@"),
            array("@wildcard@")
        );
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
        return array(
            array("expanderName(", "expanderName"),
            array("e(", "e"),
            array(".e(", "e")
        );
    }

    public function test_ignore_whitespaces_between_parenthesis()
    {
        $expectedTokens = array("type", "expander", "arg1", ",", 2, ",", "arg3", ",", 4, ")");
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
        $tokens = array();
        while ($lexer->moveNext()) {
            $tokens[] = $lexer->lookahead['value'];
        }
        return $tokens;
    }
}
