<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\MatchRegex;
use PHPUnit\Framework\TestCase;

class MatchRegexTest extends TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_match_expander($expectedResult, $expectedError, $pattern, $value)
    {
        $expander = new MatchRegex($pattern);
        $this->assertEquals($expectedResult, $expander->match($value));
        $this->assertSame($expectedError, $expander->getError());
    }

    public static function examplesProvider()
    {
        return [
            [true, null, '/^\w$/', 'a'],
            [false, 'string "aa" don\'t match pattern /^\w$/.', '/^\w$/', 'aa'],
            [false, 'Match expander require "string", got "Array(0)".', '/^\w$/', []],
        ];
    }

    public function test_that_it_only_work_with_string()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Regex pattern must be a string.');

        new MatchRegex(null);
    }

    public function test_that_it_only_work_with_valid_pattern()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Regex pattern must be a valid one.');

        new MatchRegex('///');
    }
}
