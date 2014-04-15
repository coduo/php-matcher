<?php 
namespace JsonMatcher\Tests;

use JsonMatcher\Matcher;

class MatcherTest extends \PHPUnit_Framework_TestCase
{
    private $simpleArray;
    private $matcher;

    public function setUp()
    {
        $this->simpleArray = [6, 6.66, true, false, [1, 2, 'foo'], ['foo' => 'bar'], null];
        $this->matcher = new Matcher();
    }

    public function test_match_arrays()
    {
        $this->assertTrue($this->matcher->match($this->simpleArray, $this->simpleArray));
        $this->assertTrue($this->matcher->match([], []));
        $this->assertFalse($this->matcher->match($this->simpleArray, []));
        $this->assertFalse($this->matcher->match(['foo', 1, 3], ['foo', 2, 3]));
        $this->assertFalse($this->matcher->match($this->simpleArray, [6, 6.66, false, false, [1, 2, 'foo'], ['foo' => 'bar2'], null]));
    }

    public function test_match_string()
    {
        $this->assertTrue($this->matcher->match('foo', 'foo'));
        $this->assertTrue($this->matcher->match('', ''));
        $this->assertFalse($this->matcher->match('', 'foo'));
    }

    public function test_match_booleans()
    {
        $this->assertTrue($this->matcher->match(true, true));
        $this->assertTrue($this->matcher->match(false, false));
        $this->assertFalse($this->matcher->match(true, false));
        $this->assertFalse($this->matcher->match(false, true));
    }

    public function test_type_placeholders()
    {
        $this->assertTrue($this->matcher->match(1, "@integer"));
        $this->assertTrue($this->matcher->match("michal", "@string"));
        $this->assertTrue($this->matcher->match(false, "@boolean"));
        $this->assertTrue($this->matcher->match(6.66, "@double"));
    }

    public function test_arrays_with_placeholders()
    {
        $this->assertTrue($this->matcher->match([1, 2, "foobar"], ["@integer", 2, "@string"]));
    }
}