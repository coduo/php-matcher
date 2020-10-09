<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\TestCase;

final class OrMatcherTest extends TestCase
{
    /**
     * @var PHPMatcher
     */
    protected $matcher;

    public function setUp() : void
    {
        $this->matcher = new PHPMatcher();
    }

    /**
     * @dataProvider orExamples()
     */
    public function test_matcher_with_or($value, $pattern, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->matcher->match($value, $pattern));
    }

    public static function orExamples()
    {
        return [
            ['lorem ipsum', '@string@.startsWith("lorem")||@string@.contains("lorem")', true],
            ['ipsum lorem', '@string@.startsWith("lorem")||@string@.contains("lorem")', true],
            ['norbert@coduo.pl', '@string@.isEmail()||@null@', true],
            [null, '@string@.isEmail()||@null@', true],
            ['2014-08-19', '@string@.isDateTime()||@integer@', true],
            [null, '@integer@||@string@', false],
            [1, '@integer@.greaterThan(10)||@string@.contains("10")', false],
            [[], '@array@||@null@', true],
            [[1, 2, 3], '@array@.count(3)||@null@', true],
            [null, '@array@||@null@', true],
            [null, '@array@.count(3)||@null@', true],
            ['ipsum', '@array@||@string@', true],
            ['ipsum', '@array@||@null@', false],
            [1, '@array@||@null@', false],
        ];
    }
}
