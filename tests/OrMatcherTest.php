<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\TestCase;

final class OrMatcherTest extends TestCase
{
    /**
     * @var Matcher
     */
    protected $matcher;

    public function setUp() : void
    {
        $factory = new SimpleFactory();
        $this->matcher = $factory->createMatcher();
    }

    /**
     * @dataProvider orExamples()
     */
    public function test_matcher_with_or($value, $pattern, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->matcher->match($value, $pattern));
        $this->assertSame($expectedResult, PHPMatcher::match($value, $pattern));
    }

    public static function orExamples()
    {
        return [
            ['lorem ipsum', '@string@.startsWith("lorem")||@string@.contains("lorem")', true],
            ['ipsum lorem', '@string@.startsWith("lorem")||@string@.contains("lorem")', true],
            ['norbert@coduo.pl', '@string@.isEmail()||@null@', true],
            [null, '@string@.isEmail()||@null@', true],
            [null, '@string@.isEmail()||@null@', true],
            ['2014-08-19', '@string@.isDateTime()||@integer@', true],
            [null, '@integer@||@string@', false],
            [1, '@integer@.greaterThan(10)||@string@.contains("10")', false],
        ];
    }
}
