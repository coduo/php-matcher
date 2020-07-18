<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\LowerThan;
use PHPUnit\Framework\TestCase;

class LowerThanTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            [10.5, 10, true],
            [-10.5, -20, true],
            [1, 10, false],
            [1, 1, false],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            [1, 'ipsum lorem', 'Value "ipsum lorem" is not a valid number.'],
            [5, 10, 'Value "10" is not lower than "5".'],
            [5, 5, 'Value "5" is not lower than "5".'],
        ];
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_examples($boundary, $value, $expectedResult) : void
    {
        $expander = new LowerThan($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage) : void
    {
        $expander = new LowerThan($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
