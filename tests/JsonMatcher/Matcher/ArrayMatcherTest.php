<?php
namespace JsonMatcher\Tests\Matcher;

use JsonMatcher\Matcher\ArrayMatcher;
use JsonMatcher\Matcher\ChainMatcher;
use JsonMatcher\Matcher\ScalarMatcher;
use JsonMatcher\Matcher\WildcardMatcher;

class ArrayMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayMatcher
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new ArrayMatcher(
            new ChainMatcher(array(
                new ScalarMatcher(),
                new WildcardMatcher()
            ))
        );
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match_arrays($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match_arrays($value, $pattern)
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
    }

    public static function positiveMatchData()
    {
        $simpleArr =  array(
            'users' => array(
                array(
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ),
                array(
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski'
                )
            ),
            true,
            false,
            1,
            6.66
        );

        return array(
            array($simpleArr, $simpleArr),
            array(array(), array()),
            array(array('key' => 'val'), array('key' => 'val')),
            array(array(1), array(1)),
            array(array('roles' => array('ROLE_ADMIN', 'ROLE_DEVELOPER')), array('roles' => '@wildcard@'))
        );
    }

    public static function negativeMatchData()
    {
        $simpleArr =  array(
            'users' => array(
                array(
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ),
                array(
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski'
                )
            ),
            true,
            false,
            1,
            6.66
        );

        $simpleDiff =  array(
            'users' => array(
                array(
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ),
                array(
                    'firstName' => 'Pablo',
                    'lastName' => 'Dąbrowski'
                )
            ),
            true,
            false,
            1,
            6.66
        );

        return array(
            array($simpleArr, $simpleDiff),
            array(array(1), array()),
            array(array('key' => 'val'), array('key' => 'val2')),
            array(array(1), array(2)),
            array(array('foo', 1, 3), array('foo', 2, 3))
        );
    }

}
