<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\TestCase;

final class EmptyPatternsTest extends TestCase
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
     * @dataProvider emptyPatternString
     */
    public function test_empty_pattern_in_the_json($value, $pattern, $expectedResult)
    {
        $match = $this->matcher->match($value, $pattern);
        $this->assertSame($expectedResult, $match);
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
