<?php
namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Matcher\ArrayMatcher;
use Coduo\PHPMatcher\Matcher\ChainMatcher;
use Coduo\PHPMatcher\Matcher\ExpressionMatcher;
use Coduo\PHPMatcher\Matcher\JsonMatcher;
use Coduo\PHPMatcher\Matcher\ScalarMatcher;
use Coduo\PHPMatcher\Matcher\TypeMatcher;
use Coduo\PHPMatcher\Matcher\WildcardMatcher;
use Coduo\PHPMatcher\Matcher;

class MatcherTest extends \PHPUnit_Framework_TestCase
{
    protected $matcher;

    protected $arrayValue;

    public function setUp()
    {
        $scalarMatchers = new ChainMatcher(array(
            new ExpressionMatcher(),
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

        $this->assertTrue(match(
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
        $this->assertTrue(match(
            'Norbert Orzechowicz',
            '@string@'
        ));
        $this->assertTrue($this->matcher->match(
            6.66,
            '@double@'
        ));
        $this->assertTrue(match(
            6.66,
            '@double@'
        ));
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
}
