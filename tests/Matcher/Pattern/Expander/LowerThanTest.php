<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\LowerThan;
use PHPUnit\Framework\TestCase;

class LowerThanTest extends TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_examples($boundary, $value, $expectedResult)
    {
        $expander = new LowerThan($boundary);
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    public static function examplesProvider()
    {
        return [
            [10.5, 10, true],
            [-10.5, -20, true],
            [1, 10, false],
            [1, 1, false],
            ['+ 2 day','today',true],
            ['2018-02-06T04:20:33','2017-02-06T04:20:33',true],
            ['2017-02-06T04:20:33','2018-02-06T04:20:33',false],
        ];
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage)
    {
        $expander = new LowerThan($boundary);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return [
            [1, 'ipsum lorem', 'Value "ipsum lorem" is not a valid number nor a date.'],
            ['2017-02-06T04:20:33', 'ipsum lorem', 'Value "ipsum lorem" is not a valid number nor a date.'],
            [5, 10, 'Value "10" is not lower than "5".'],
            [5, 5, 'Value "5" is not lower than "5".'],
            [5,'today', 'Value "today" is not the same type as "5", booth must date or a number.'],
            ['today',5, 'Value "5" is not the same type as "today", booth must date or a number.'],
        ];
    }
}
