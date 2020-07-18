<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\WildcardMatcher;
use PHPUnit\Framework\TestCase;

class WildcardMatcherTest extends TestCase
{
    public static function data()
    {
        return [
            ['@integer@'],
            ['foobar'],
            [true],
            [6.66],
            [['bar']],
            [new \stdClass],
        ];
    }

    public static function positivePatterns()
    {
        return [
            ['@*@'],
            ['@wildcard@'],
        ];
    }

    /**
     * @dataProvider data
     */
    public function test_positive_match($pattern) : void
    {
        $matcher = new WildcardMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->match('*', $pattern));
    }

    /**
     * @dataProvider positivePatterns
     */
    public function test_positive_can_match($pattern) : void
    {
        $matcher = new WildcardMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->canMatch($pattern));
    }

    public function test_negative_can_match() : void
    {
        $matcher = new WildcardMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($matcher->canMatch('*'));
    }
}
