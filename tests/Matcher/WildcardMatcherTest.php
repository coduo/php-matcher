<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\WildcardMatcher;
use PHPUnit\Framework\TestCase;

class WildcardMatcherTest extends TestCase
{

    /**
     * @dataProvider data
     */
    public function test_positive_match($pattern)
    {
        $matcher = new WildcardMatcher();
        $this->assertTrue($matcher->match('*', $pattern));
    }

    /**
     * @dataProvider positivePatterns
     */
    public function test_positive_can_match($pattern)
    {
        $matcher = new WildcardMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    public function test_negative_can_match()
    {
        $matcher = new WildcardMatcher();
        $this->assertFalse($matcher->canMatch('*'));
    }

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
}
