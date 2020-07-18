<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\Count;
use PHPUnit\Framework\TestCase;

class CountTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            [1, ['ipsum'], true],
            [2, ['foo', 1], true],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            [2, [1, 2, 3], 'Expected count of Array(3) is 2.'],
            [2, new \DateTime(), 'Count expander require "array", got "\\DateTime".'],
        ];
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_matching_values($needle, $haystack, $expectedResult) : void
    {
        $expander = new Count($needle);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage) : void
    {
        $expander = new Count($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
