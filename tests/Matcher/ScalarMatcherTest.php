<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\Modifier\CaseInsensitive;
use Coduo\PHPMatcher\Matcher\ScalarMatcher;
use PHPUnit\Framework\TestCase;

class ScalarMatcherTest extends TestCase
{
    /**
     * @dataProvider positiveCanMatches
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new ScalarMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatches
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new ScalarMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatches
     */
    public function test_positive_matches($value, $pattern)
    {
        $matcher = new ScalarMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatches
     */
    public function test_negative_matches($value, $pattern)
    {
        $matcher = new ScalarMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new ScalarMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    /**
     * @dataProvider positiveCaseInsensitiveMatches
     */
    public function test_positive_case_insensitive_matches($value, $pattern)
    {
        $matcher = $this->caseInsensitiveMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeCaseInsensitiveMatches
     */
    public function test_negative_case_insensitive_matches($value, $pattern)
    {
        $matcher = $this->caseInsensitiveMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeCaseInsensitiveMatchDescription
     */
    public function test_negative_case_insensitive_match_description($value, $pattern, $error)
    {
        $matcher = $this->caseInsensitiveMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    public static function negativeMatches()
    {
        return [
            [false, 'false'],
            [false, 0],
            [true, 1],
            ['array', []],
        ];
    }

    public static function positiveMatches()
    {
        return [
            [1, 1],
            ['michal', 'michal'],
            [false, false],
            [6.66, 6.66],
        ];
    }

    public static function positiveCanMatches()
    {
        return [
            [1],
            ['michal'],
            [true],
            [false],
            [6.66],
        ];
    }

    public static function negativeCanMatches()
    {
        return [
            [new \stdClass],
            [[]]
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            ['test', 'norbert', '"test" does not match "norbert".'],
            [new \stdClass,  1, '"\\stdClass" does not match "1".'],
            [1.1, false, '"1.1" does not match "false".'],
            [false, ['foo', 'bar'], '"false" does not match "Array(2)".'],
        ];
    }

    public static function positiveCaseInsensitiveMatches()
    {
        return [
            ['michal', 'Michal'],
            ['Michal', 'michal'],
            ['fooBar', 'FOObAR'],
            ['Bazinga', 'baZIngA']
        ];
    }

    public static function negativeCaseInsensitiveMatches()
    {
        return [
            ['michal', 'Michall'],
            ['fooBar', 'barFoo'],
            ['Bazinga', 'BaIng']
        ];
    }

    public static function negativeCaseInsensitiveMatchDescription()
    {
        return [
            ['michal', 'Michall', '"michal" does not case-insensitive match "Michall".'],
            ['fooBar', 'barFoo', '"fooBar" does not case-insensitive match "barFoo".'],
            ['Bazinga', 'BaIng', '"Bazinga" does not case-insensitive match "BaIng".']
        ];
    }

    private function caseInsensitiveMatcher(): ScalarMatcher
    {
        $matcher = new ScalarMatcher();
        $matcher->applyModifier(new CaseInsensitive());

        return $matcher;
    }
}
