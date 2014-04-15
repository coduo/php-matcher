<?php
namespace JsonMatcher\Tests;

use JsonMatcher\Matcher\TypeMatcher;

class TypeMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_match()
    {
        $matcher = new TypeMatcher();
        $this->assertTrue($matcher->canMatch('@integer@'));
        $this->assertTrue($matcher->canMatch('@string@'));
        $this->assertTrue($matcher->canMatch('@boolean@'));
        $this->assertFalse($matcher->canMatch('@integer'));
        $this->assertFalse($matcher->canMatch("qweqwe"));
        $this->assertFalse($matcher->canMatch(1));
        $this->assertFalse($matcher->canMatch("@string"));
    }

    public function test_type_match()
    {
        $matcher = new TypeMatcher();
        $this->assertTrue($matcher->match(false, '@boolean@'));
        $this->assertTrue($matcher->match("Norbert", '@string@'));
        $this->assertTrue($matcher->match(1, '@integer@'));
        $this->assertTrue($matcher->match(1.1, '@double@'));

        $this->assertFalse($matcher->match("test", '@boolean@'));
        $this->assertFalse($matcher->match(new \stdClass(), '@string@'));
        $this->assertFalse($matcher->match(1.1, '@integer@'));
        $this->assertFalse($matcher->match(false, '@double@'));
    }
}
