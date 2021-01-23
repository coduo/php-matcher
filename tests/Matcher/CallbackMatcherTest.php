<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\CallbackMatcher;
use PHPUnit\Framework\TestCase;

class CallbackMatcherTest extends TestCase
{
    public function test_positive_can_match() : void
    {
        $matcher = new CallbackMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->canMatch(fn () => true));
    }

    public function test_negative_can_match() : void
    {
        $matcher = new CallbackMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($matcher->canMatch(new \DateTime()));
        $this->assertFalse($matcher->canMatch('SIN'));
    }

    public function test_positive_matches() : void
    {
        $matcher = new CallbackMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->match(2, fn ($value) => true));
        $this->assertTrue($matcher->match('true', fn ($value) => $value));
    }

    public function test_negative_matches() : void
    {
        $matcher = new CallbackMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($matcher->match(2, fn ($value) => false));
        $this->assertFalse($matcher->match(0, fn ($value) => $value));
        $this->assertFalse($matcher->match(null, fn ($value) => $value));
        $this->assertFalse($matcher->match([], fn ($value) => $value));
    }
}
