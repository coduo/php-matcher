<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\GreaterThan;
use PHPUnit\Framework\TestCase;

class GreaterThanTest extends TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_examples($boundary, $value, $expectedResult)
    {
        $expander = new GreaterThan($boundary);
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    public static function examplesProvider()
    {
        return [
            [10, 10.5, true],
            [-20, -10.5, true],
            [10, 1, false],
            [1, 1, false],
            [10, '20', true],
            ['+ 2 day','today',false],
            ['2018-02-06T04:20:33','2017-02-06T04:20:33',false],
            ['2017-02-06T04:20:33','2018-02-06T04:20:33',true],
        ];
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage)
    {
        $expander = new GreaterThan($boundary);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return [
            [1, 'ipsum lorem', 'Value "ipsum lorem" is not a valid number nor a date.'],
            [10, 5, 'Value "5" is not greater than "10".'],
            [5, 5, 'Value "5" is not greater than "5".'],
            [5,'today', 'Value "today" is not the same type as "5", booth must date or a number.'],
            ['today',5, 'Value "5" is not the same type as "today", booth must date or a number.'],
        ];
    }
}
