<?php

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;

class JsonMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Matcher\JsonMatcher
     */
    private $matcher;

    public function setUp()
    {
        $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer());
        $scalarMatchers = new Matcher\ChainMatcher(array(
            new Matcher\CallbackMatcher(),
            new Matcher\ExpressionMatcher(),
            new Matcher\NullMatcher(),
            new Matcher\StringMatcher($parser),
            new Matcher\IntegerMatcher($parser),
            new Matcher\BooleanMatcher(),
            new Matcher\DoubleMatcher($parser),
            new Matcher\NumberMatcher(),
            new Matcher\ScalarMatcher(),
            new Matcher\WildcardMatcher(),
        ));
        $this->matcher = new Matcher\JsonMatcher(new Matcher\ChainMatcher(array(
            $scalarMatchers,
            new Matcher\ArrayMatcher($scalarMatchers, $parser)
        )));
    }

    /**
     * @dataProvider positivePatterns
     */
    public function test_positive_can_match($pattern)
    {
        $this->assertTrue($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativePatterns
     */
    public function test_negative_can_match($pattern)
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatches
     */
    public function test_positive_matches($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern), $this->matcher->getError());
    }

    /**
     * @dataProvider normalizationRequiredDataProvider
     */
    public function test_positive_matches_after_normalization($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern), $this->matcher->getError());
    }

    /**
     * @dataProvider negativeMatches
     */
    public function test_negative_matches($value, $pattern)
    {
        $this->assertFalse($this->matcher->match($value, $pattern), $this->matcher->getError());
    }

    public function test_error_when_matching_fail()
    {
        $value = json_encode(array(
            'users' => array(
                array('name' => 'Norbert'),
                array('name' => 'Michał')
            )
        ));
        $pattern = json_encode(array(
            'users' => array(
                array('name' => '@string@'),
                array('name' => '@boolean@')
            )
        ));

        $this->assertFalse($this->matcher->match($value, $pattern));
        $this->assertEquals($this->matcher->getError(), '"Michał" does not match "@boolean@".');
    }

    public function test_error_when_path_in_nested_pattern_does_not_exist()
    {
        $value = json_encode(array('foo' => array('bar' => array('baz' => 'bar value'))));
        $pattern = json_encode(array('foo' => array('bar' => array('faz' => 'faz value'))));

        $this->assertFalse($this->matcher->match($value, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][baz] in pattern.');
    }

    public function test_error_when_path_in_nested_value_does_not_exist()
    {
        $value = json_encode(array('foo' => array('bar' => array())));
        $pattern = json_encode(array('foo' => array('bar' => array('faz' => 'faz value'))));

        $this->assertFalse($this->matcher->match($value, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][faz] in value.');
    }

    public function test_error_when_json_pattern_is_invalid()
    {
        $value = '{"test": "value"}';
        $pattern = '{"test": "@string@",}';

        $this->assertFalse($this->matcher->match($value, $pattern));

        $this->assertEquals($this->matcher->getError(), 'Invalid given JSON of pattern. Syntax error, malformed JSON');
    }

    public function test_error_when_json_value_is_invalid()
    {
        $value = '{"test": "value",}';
        $pattern = '{"test": "@string@"}';

        $this->assertFalse($this->matcher->match($value, $pattern));

        $this->assertEquals($this->matcher->getError(), 'Invalid given JSON of value. Syntax error, malformed JSON');
    }

    public static function positivePatterns()
    {
        return array(
            array(json_encode(array('Norbert', 'Michał'))),
            array(json_encode(array('Norbert', '@string@'))),
            array(json_encode('test')),
        );
    }

    public static function negativePatterns()
    {
        return array(
            array('@string@'),
            array('["Norbert", '),
        );
    }

    public static function positiveMatches()
    {
        return array(
            array(
                '{"users":["Norbert","Michał"]}',
                '{"users":["@string@","@string@"]}'
            ),
            array(
                '{"users":["Norbert","Michał"]}',
                '{"users":["@string@","@...@"]}'
            ),
            array(
                '{"users":["Norbert","Michał"]}',
                '{"users":["@string@",@...@]}'
            ),
            array(
                '{"numbers":[1,2]}',
                '{"numbers":[@integer@, @integer@]}'
            ),
            array(
                '{"foobar":[1.22, 2, "hello"]}',
                '{"foobar":[@double@, @integer@, @string@]}'
            ),
            array(
                '{"null":[null]}',
                '{"null":[@null@]}'
            ),
            array(
                '{"null":null}',
                '{"null":@null@}'
            ),
            array(
                '{"username":null,"some_data":"test"}',
                '{"username":null, "some_data": @string@}'
            ),
            array(
                '{"null":null}',
                '{"null":null}'
            ),
            array(
                '{"users":["Norbert","Michał",[]]}',
                '{"users":["Norbert","@string@",@...@]}'
            ),
            array(
                '{"users":[{"firstName":"Norbert","lastName":"Orzechowicz","roles":["ROLE_USER", "ROLE_DEVELOPER"]}]}',
                '{"users":[{"firstName":"Norbert","lastName":"Orzechowicz","roles":"@wildcard@"}]}'
            ),
            array(
                '[{"name": "Norbert"},{"name":"Michał"},{"name":"Bob"},{"name":"Martin"}]',
                '[{"name": "Norbert"},@...@]'
            )
        );
    }

    public static function negativeMatches()
    {
        return array(
            array(
                '{"users":["Norbert","Michał"]}',
                '{"users":["Michał","@string@"]}'
            ),
            array(
                '{"users":["Norbert","Michał", "John"], "stuff": [1, 2, 3]}',
                '{"users":["@string@", @...@], "stuff": [1, 2]}'
            ),
            array(
                '{this_is_not_valid_json',
                '{"users":["Michał","@string@"]}'
            ),
            array(
                '{"status":"ok","data":[]}',
                '{"status":"ok","data":[{"id": 4,"code":"123987","name":"Anvill","short_description":"ACME Anvill","url":"http://test-store.example.com/p/123987","image":{"url":"http://test-store.example.com/i/123987-0.jpg","description":"ACME Anvill"},"price":95,"promotion_description":"Anvills sale"},{"id": 5,"code":"123988","name":"Red Anvill","short_description":"Red ACME Anvill","url":"http://test-store.example.com/p/123988","image":{"url":"http://test-store.example.com/i/123988-0.jpg","description":"ACME Anvill"},"price":44.99,"promotion_description":"Red is cheap"}]}'
            ),
            array(
                '{"foo":"foo val","bar":"bar val"}',
                '{"foo":"foo val"}'
            ),
            array(
                array(),
                '[]'
            )
        );
    }

    public static function normalizationRequiredDataProvider()
    {
        return array(
            array(
                '{"name": "Norbert"}',
                '{"name": @string@}'
            ),
            array(
                '{"name": 25}',
                '{"name": @number@}'
            ),
            array(
                '{"name": 25}',
                '{"name": @integer@}'
            ),
            array(
                '{"name": true}',
                '{"name": @boolean@}'
            ),
            array(
                '{"name": ["Norbert", "Michał"]}',
                '{"name": ["Norbert", @...@]}'
            ),
            array(
                '{"name": "Norbert", "roles": ["ADMIN", "USER"]}',
                '{"name": @string@, "roles": [@string@, @string@]}'
            ),
        );
    }
}
