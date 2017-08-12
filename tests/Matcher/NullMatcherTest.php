<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\NullMatcher;
use PHPUnit\Framework\TestCase;

class NullMatcherTest extends TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new NullMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new NullMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $matcher = new NullMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $matcher = new NullMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new NullMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    public static function positiveCanMatchData()
    {
        return [
            ["@null@"],
            [null]
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [null, "@null@"],
            [null, null],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ["@null"],
            ["null"],
            [0]
        ];
    }

    public static function negativeMatchData()
    {
        return [
            ["null", "@null@"],
            [0,  "@null@"]
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            ["test", "@boolean@", "string \"test\" does not match null."],
            [new \stdClass,  "@string@", "object \"\\stdClass\" does not match null."],
            [1.1, "@integer@", "double \"1.1\" does not match null."],
            [false, "@double@", "boolean \"false\" does not match null."],
            [1, "@array@", "integer \"1\" does not match null."]
        ];
    }
}
