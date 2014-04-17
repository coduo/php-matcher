<?php
namespace JsonMatcher\Tests\Matcher;

use JsonMatcher\Matcher\ArrayMatcher;
use JsonMatcher\Matcher\ChainMatcher;
use JsonMatcher\Matcher\JsonMatcher;
use JsonMatcher\Matcher\ScalarMatcher;
use JsonMatcher\Matcher\TypeMatcher;
use JsonMatcher\Matcher\WildcardMatcher;

class JsonMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonMatcher
     */
    private $matcher;

    public function setUp()
    {
        $scalarMatchers = new ChainMatcher(array(
            new TypeMatcher(),
            new ScalarMatcher(),
            new WildcardMatcher()
        ));
        $this->matcher = new JsonMatcher(new ChainMatcher(array(
            $scalarMatchers,
            new ArrayMatcher($scalarMatchers)
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
    public function test_negative_can_match()
    {
        $this->assertFalse($this->matcher->canMatch('*'));
    }

    /**
     * @dataProvider positiveMatches
     */
    public function test_positive_matches($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatches
     */
    public function test_negative_matches($value, $pattern)
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
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
            array('["Norbert",@string@]'),
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
                '{"users":[{"firstName":"Norbert","lastName":"Orzechowicz","roles":["ROLE_USER", "ROLE_DEVELOPER"]}]}',
                '{"users":[{"firstName":"Norbert","lastName":"Orzechowicz","roles":"@wildcard@"}]}'
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
                '{this_is_not_valid_json',
                '{"users":["Michał","@string@"]}'
            ),
            array(
                array(),
                '[]'
            )
        );
    }
}
