<?php
namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Matcher\ArrayMatcher;
use Coduo\PHPMatcher\Matcher\CaptureMatcher;
use Coduo\PHPMatcher\Matcher\CallbackMatcher;
use Coduo\PHPMatcher\Matcher\ChainMatcher;
use Coduo\PHPMatcher\Matcher\ExpressionMatcher;
use Coduo\PHPMatcher\Matcher\JsonMatcher;
use Coduo\PHPMatcher\Matcher\ScalarMatcher;
use Coduo\PHPMatcher\Matcher\TypeMatcher;
use Coduo\PHPMatcher\Matcher\WildcardMatcher;
use Coduo\PHPMatcher\Matcher;

class MatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Matcher
     */
    protected $matcher;

    protected $arrayValue;

    protected $captureMatcher;

    public function setUp()
    {
        $this->captureMatcher = new CaptureMatcher();

        $scalarMatchers = new ChainMatcher(array(
            new CallbackMatcher(),
            new ExpressionMatcher(),
            $this->captureMatcher,
            new CaptureMatcher(),
            new TypeMatcher(),
            new ScalarMatcher(),
            new WildcardMatcher()
        ));

        $arrayMatcher = new ArrayMatcher($scalarMatchers);

        $this->matcher = new Matcher(new ChainMatcher(array(
            $scalarMatchers,
            $arrayMatcher,
            new JsonMatcher($arrayMatcher)
        )));
    }

    public function test_matcher_with_array_value()
    {
        $value = array(
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

        $expecation = array(
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
        );

        $this->assertTrue($this->matcher->match($value, $expecation));
        $this->assertTrue(match($value, $expecation));
    }

    /**
     * @dataProvider scalarValues
     */
    public function test_matcher_with_scalar_values($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
        $this->assertTrue(match($value, $pattern));
    }

    public function scalarValues()
    {
        return array(
            array('Norbert Orzechowicz', '@string@'),
            array(6.66, '@double@'),
            array(1, '@integer@'),
            array(array('foo'), '@array@')
        );
    }

    public function test_matcher_with_json()
    {
        $json = '
        {
            "users":[
                {
                    "id": 131,
                    "firstName": "Norbert",
                    "lastName": "Orzechowicz",
                    "enabled": true,
                    "roles": ["ROLE_DEVELOPER"]
                },
                {
                    "id": 132,
                    "firstName": "Michał",
                    "lastName": "Dąbrowski",
                    "enabled": false,
                    "roles": ["ROLE_DEVELOPER"]
                }
            ],
            "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
            "nextPage": "http:\/\/example.com\/api\/users\/3?limit=2"
        }';
        $jsonPattern = '
        {
            "users":[
                {
                    "id": "@integer@",
                    "firstName":"Norbert",
                    "lastName":"Orzechowicz",
                    "enabled": "@boolean@",
                    "roles": "@array@"
                },
                {
                    "id": "@integer@",
                    "firstName": "Michał",
                    "lastName": "Dąbrowski",
                    "enabled": "expr(value == false)",
                    "roles": "@array@"
                }
            ],
            "prevPage": "@string@",
            "nextPage": "@string@"
        }';

        $this->assertTrue($this->matcher->match($json, $jsonPattern));
        $this->assertTrue(match($json, $jsonPattern));
    }

    public function test_matcher_with_captures()
    {
        $this->assertTrue($this->matcher->match(
            array('foo' => 'bar', 'user' => array('id' => 5)),
            array('foo' => 'bar', 'user' => array('id' => ':uid:'))
        ));
        $this->assertEquals($this->captureMatcher['uid'], 5);
    }
    
    function test_matcher_with_callback()
    {
        $this->assertTrue($this->matcher->match('test', function($value) { return $value === 'test';}));
        $this->assertFalse($this->matcher->match('test', function($value) { return $value !== 'test';}));
    }

    function test_matcher_with_wildcard()
    {
        $this->assertTrue($this->matcher->match('test', '@*@'));
        $this->assertTrue($this->matcher->match('test', '@wildcard@'));
    }
}
