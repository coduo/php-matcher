<?php
namespace JsonMatcher\Tests;

use JsonMatcher\Matcher\ArrayMatcher;
use JsonMatcher\Matcher\ChainMatcher;
use JsonMatcher\Matcher\ScalarMatcher;

class ArrayMatcherTest extends \PHPUnit_Framework_TestCase
{
    private $simpleArray;

    public function setUp()
    {
        $this->simpleArray = [
            'users' => [
                [
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ],
                [
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski'
                ]
            ],
            true,
            false,
            1,
            6.66
        ];
    }

    public function test_match_arrays()
    {
        $chain = new ChainMatcher();
        $chain->addMatcher(new ScalarMatcher());
        $matcher = new ArrayMatcher($chain);

        $this->assertTrue($matcher->match($this->simpleArray, $this->simpleArray));
        $this->assertTrue($matcher->match([], []));
        $this->assertFalse($matcher->match($this->simpleArray, []));
        $this->assertFalse($matcher->match(['foo', 1, 3], ['foo', 2, 3]));
        $this->assertFalse($matcher->match($this->simpleArray, [6, 6.66, false, false, [1, 2, 'foo'], ['foo' => 'bar2'], null]));
        $this->assertFalse($matcher->match($this->simpleArray, [
            'users' => [
                [
                    'firstName' => 'Pawel',
                    'lastName' => 'Orzechowicz'
                ],
                [
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski'
                ]
            ]
        ]));
    }
}
