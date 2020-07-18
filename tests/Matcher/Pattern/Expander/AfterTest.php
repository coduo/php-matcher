<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\After;
use PHPUnit\Framework\TestCase;

class AfterTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            ['+ 2 day', 'today', false],
            ['2018-02-06T04:20:33', '2017-02-06T04:20:33', false],
            ['2017-02-06T04:20:33', '2018-02-06T04:20:33', true],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            ['today', 'ipsum lorem', 'Value "ipsum lorem" is not a valid date.'],
            ['2017-02-06T04:20:33', 'ipsum lorem', 'Value "ipsum lorem" is not a valid date.'],
            ['today', 5, 'After expander require "string", got "5".'],
        ];
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_examples($boundary, $value, $expectedResult) : void
    {
        $expander = new After($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage) : void
    {
        $expander = new After($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
