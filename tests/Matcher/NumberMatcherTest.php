<?php

declare(strict_types=1);
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\NumberMatcher;
use PHPUnit\Framework\TestCase;

class NumberMatcherTest extends TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new NumberMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new NumberMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $matcher = new NumberMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $matcher = new NumberMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new NumberMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    public static function positiveCanMatchData()
    {
        return [
            ["@number@"]
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [10.1, "@number@"],
            [10, "@number@"],
            ["25", "@number@"],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ["@number"],
            ["number"],
            [1]
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [["test"], "@number@"],
            [new \DateTime(),  "@number@"],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  "@number@", "object \"\\stdClass\" is not a valid number."],
            [false, "@number@", "boolean \"false\" is not a valid number."],
            [['test'], "@number@", "array \"Array(1)\" is not a valid number."]
        ];
    }
}
