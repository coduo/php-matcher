<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\ArrayMatcher;
use Coduo\PHPMatcher\Matcher\ChainMatcher;
use Coduo\PHPMatcher\Matcher\ScalarMatcher;
use Coduo\PHPMatcher\Matcher\WildcardMatcher;

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

    public function test_negative_match_when_cant_find_matcher_that_can_match_array_element()
    {
        $matcher = new ArrayMatcher(
            new ChainMatcher(array(
                new WildcardMatcher()
            ))
        );

        $this->assertFalse($matcher->match(array('test' => 1), array('test' => 1)));
    }

    public function test_error_when_path_in_pattern_does_not_exist()
    {
        $this->assertFalse($this->matcher->match(array('foo' => 'foo value'), array('bar' => 'bar value')));
        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo] in pattern.');
    }

    public function test_error_when_path_in_nested_pattern_does_not_exist()
    {
        $array = array('foo' => array('bar' => array('baz' => 'bar value')));
        $pattern = array('foo' => array('bar' => array('faz' => 'faz value')));

        $this->assertFalse($this->matcher->match($array,$pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][baz] in pattern.');
    }

    public function test_error_when_path_in_value_does_not_exist()
    {
        $array = array('foo' => 'foo');
        $pattern = array('foo' => 'foo', 'bar' => 'bar');

        $this->assertFalse($this->matcher->match($array, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [bar] in value.');
    }

    public function test_error_when_path_in_nested_value_does_not_exist()
    {
        $array = array('foo' => array('bar' => array()));
        $pattern = array('foo' => array('bar' => array('faz' => 'faz value')));

        $this->assertFalse($this->matcher->match($array, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][faz] in value.');
    }

    public function test_error_when_matching_fail()
    {
        $this->assertFalse($this->matcher->match(array('foo' => 'foo value'), array('foo' => 'bar value')));
        $this->assertEquals($this->matcher->getError(), '"foo value" does not match "bar value".');
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
            array(array("status" => "ok", "data" => array(array('foo'))), array("status" => "ok", "data" => array())),
            array(array(1), array()),
            array(array('key' => 'val'), array('key' => 'val2')),
            array(array(1), array(2)),
            array(array('foo', 1, 3), array('foo', 2, 3)),
            array(array(), array('foo' => 'bar'))
        );
    }
}
