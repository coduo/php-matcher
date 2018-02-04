<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\Count;
use PHPUnit\Framework\TestCase;

class CountTest extends TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_matching_values($needle, $haystack, $expectedResult)
    {
        $expander = new Count($needle);
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    public static function examplesProvider()
    {
        return [
            [1, ['ipsum'], true],
            [2, ['foo', 1], true],
        ];
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage)
    {
        $expander = new Count($boundary);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return [
            [2, [1, 2, 3], 'Expected count of Array(3) is 2.'],
            [2, new \DateTime(), 'Count expander require "array", got "\\DateTime".'],
        ];
    }
}
