<?php

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;

class TextMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Matcher\TextMatcher
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
        $this->matcher = new Matcher\TextMatcher(
            new Matcher\ChainMatcher(array(
                $scalarMatchers,
                new Matcher\ArrayMatcher($scalarMatchers, $parser)
            )),
            $parser
        );
    }

    /**
     * @dataProvider matchingData
     */
    public function test_positive_matches($value, $pattern, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->matcher->match($value, $pattern));
    }

    public function matchingData()
    {
        return array(
            array(
                "lorem ipsum lol lorem 24 dolorem",
                "lorem ipsum @string@.startsWith(\"lo\") lorem @number@ dolorem",
                true
            ),
            array(
                "lorem ipsum 24 dolorem",
                "lorem ipsum @integer@",
                false
            ),
            array(
                "/users/12345/active",
                "/users/@integer@.greaterThan(0)/active",
                true
            )
        );
    }
}
