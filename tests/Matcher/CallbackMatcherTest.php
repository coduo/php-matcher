<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\CallbackMatcher;
use PHPUnit\Framework\TestCase;

class CallbackMatcherTest extends TestCase
{
    public function test_positive_can_match()
    {
        $matcher = new CallbackMatcher();
        $this->assertTrue($matcher->canMatch(function () {
            return true;
        }));
    }

    public function test_negative_can_match()
    {
        $matcher = new CallbackMatcher();
        $this->assertFalse($matcher->canMatch(new \DateTime()));
        $this->assertFalse($matcher->canMatch('SIN'));
    }

    public function test_positive_matches()
    {
        $matcher = new CallbackMatcher();
        $this->assertTrue($matcher->match(2, function ($value) {
            return true;
        }));
        $this->assertTrue($matcher->match('true', function ($value) {
            return $value;
        }));
    }

    public function test_negative_matches()
    {
        $matcher = new CallbackMatcher();
        $this->assertFalse($matcher->match(2, function ($value) {
            return false;
        }));
        $this->assertFalse($matcher->match(0, function ($value) {
            return $value;
        }));
        $this->assertFalse($matcher->match(null, function ($value) {
            return $value;
        }));
        $this->assertFalse($matcher->match([], function ($value) {
            return $value;
        }));
    }
}
