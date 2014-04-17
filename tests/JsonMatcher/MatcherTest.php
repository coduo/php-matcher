<?php
namespace JsonMatcher\Tests;

use JsonMatcher\Matcher\ArrayMatcher;
use JsonMatcher\Matcher\ChainMatcher;
use JsonMatcher\Matcher\ScalarMatcher;
use JsonMatcher\Matcher\TypeMatcher;
use JsonMatcher\Matcher\WildcardMatcher;
use JsonMatcher\Matcher;

class MatcherTest extends \PHPUnit_Framework_TestCase
{
    protected $matcher;

    protected $arrayValue;

    public function setUp()
    {
        $scalarMatchers = new ChainMatcher(array(
            new TypeMatcher(),
            new ScalarMatcher(),
            new WildcardMatcher()
        ));
        $this->matcher = new Matcher(new ChainMatcher(array(
            $scalarMatchers,
            new ArrayMatcher($scalarMatchers)
        )));

        $this->arrayValue = array(
            'users' => array(
                array(
                    'id' => 1,
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz',
                    'enabled' => true
                ),
                array(
                    'id' => 2,
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski',
                    'enabled' => true,
                )
            ),
            'readyToUse' => true,
            'data' => new \stdClass(),
        );
    }

    public function test_matcher_with_array_value()
    {
        $this->assertTrue($this->matcher->match(
            $this->arrayValue,
            array(
                'users' => array(
                    array(
                        'id' => '@integer@',
                        'firstName' => '@string@',
                        'lastName' => 'Orzechowicz',
                        'enabled' => '@boolean@'
                    ),
                    array(
                        'id' => '@integer@',
                        'firstName' => '@string@',
                        'lastName' => 'Dąbrowski',
                        'enabled' => '@boolean@',
                    )
                ),
                'readyToUse' => true,
                'data' => '@wildcard@',
            )
        ));
    }

    public function test_matcher_with_scalar_values()
    {
        $this->assertTrue($this->matcher->match(
            'Norbert Orzechowicz',
            '@string@'
        ));
        $this->assertTrue($this->matcher->match(
            6.66,
            '@double@'
        ));
    }
}
