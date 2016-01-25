<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\CallbackMatcher;

class CallbackMatcherTest extends \PHPUnit_Framework_TestCase
{
    function test_positive_can_match()
    {
        $matcher = new CallbackMatcher();
        $this->assertTrue($matcher->canMatch(function() { return true; }));
    }

    function test_negative_can_match()
    {
        $matcher = new CallbackMatcher();
        $this->assertFalse($matcher->canMatch(new \DateTime()));
        $this->assertFalse($matcher->canMatch('SIN'));
    }

    function test_positive_matches()
    {
        $matcher = new CallbackMatcher();
        $this->assertTrue($matcher->match(2, function($value) { return true; }));
        $this->assertTrue($matcher->match('true', function($value) { return $value; }));
    }

    function test_negative_matches()
    {
        $matcher = new CallbackMatcher();
        $this->assertFalse($matcher->match(2, function($value) { return false; }));
        $this->assertFalse($matcher->match(0, function($value) { return $value; }));
        $this->assertFalse($matcher->match(null, function($value) { return $value; }));
        $this->assertFalse($matcher->match(array(), function($value) { return $value; }));
    }
}
