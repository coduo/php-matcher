<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\ArrayMatcher;
use Coduo\PHPMatcher\Matcher\ChainMatcher;
use Coduo\PHPMatcher\Matcher\JsonMatcher;
use Coduo\PHPMatcher\Matcher\ScalarMatcher;
use Coduo\PHPMatcher\Matcher\TypeMatcher;
use Coduo\PHPMatcher\Matcher\WildcardMatcher;

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
                '{"numbers":[1,2]}',
                '{"numbers":[@integer@, @integer@]}'
            ),
            array(
                '{"foobar":[1.22, 2, "hello"]}',
                '{"foobar":[@double@, @integer@, @string@]}'
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
}
