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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Regex pattern must be a string.
     */
    public function test_that_it_only_work_with_string()
    {
        new MatchRegex(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Regex pattern must be a valid one.
     */
    public function test_that_it_only_work_with_valid_pattern()
    {
        new MatchRegex('///');
    }
}
