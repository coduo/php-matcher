<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Factory\MatcherFactory;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\TestCase;

final class EmptyPatternsTest extends TestCase
{
    /**
     * @var Matcher
     */
    protected $matcher;

    public function setUp() : void
    {
        $factory = new MatcherFactory();
        $this->matcher = $factory->createMatcher();
    }

    /**
     * @dataProvider emptyPatternString
     */
    public function test_empty_pattern_in_the_json($value, $pattern, $expectedResult)
    {
        $match = $this->matcher->match($value, $pattern);
        $this->assertSame($expectedResult, $match);
        $this->assertSame($expectedResult, PHPMatcher::match($value, $pattern));
    }

    public static function emptyPatternString()
    {
        return [
            [
                '', '', true,
                '123', '', false,
                ' ', '', false,
                null, '', false,
                1, '', false,
                0, '', false,
                '{"name": "123"}', '{"name": ""}', false,
                '{"name": ""}', '{"name": ""}', true,
            ],
        ];
    }
}
