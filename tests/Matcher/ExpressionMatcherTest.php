<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\ExpressionMatcher;
use PHPUnit\Framework\TestCase;

class ExpressionMatcherTest extends TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new ExpressionMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new ExpressionMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $matcher = new ExpressionMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $matcher = new ExpressionMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new ExpressionMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    /**
     * @dataProvider positiveRegexMatchData
     */
    public function test_positive_regex_matches($value, $pattern)
    {
        $matcher = new ExpressionMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeRegexMatchData
     */
    public function test_negative_regex_matches($value, $pattern)
    {
        $matcher = new ExpressionMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    public static function positiveCanMatchData()
    {
        return [
            ['expr(1 > 2)'],
            ["expr(value == 'foo')"],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@integer'],
            ['expr('],
            ['@string'],
            [new \stdClass],
            [['foobar']]
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [4, 'expr(value > 2)'],
            ['foo', "expr(value == 'foo')"],
            [new \DateTime('2014-04-01'), "expr(value.format('Y-m-d') == '2014-04-01')"]
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [4, 'expr(value < 2)'],
            ['foo', "expr(value != 'foo')"],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [4, 'expr(value < 2)', '"expr(value < 2)" expression fails for value "4".'],
            [
                new \DateTime('2014-04-01'),
                "expr(value.format('Y-m-d') == '2014-04-02')",
                "\"expr(value.format('Y-m-d') == '2014-04-02')\" expression fails for value \"\\DateTime\"."
            ],
        ];
    }

    public static function positiveRegexMatchData()
    {
        return [
            ['Cakper', 'expr(value matches "/Cakper/")'],
            ['Cakper', 'expr(not(value matches "/Yaboomaster/"))'],
        ];
    }

    public static function negativeRegexMatchData()
    {
        return [
            ['Cakper', 'expr(not(value matches "/Cakper/"))'],
            ['Cakper', 'expr(value matches "/Yaboomaster/")'],
        ];
    }
}
