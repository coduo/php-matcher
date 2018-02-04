<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\InArray;
use PHPUnit\Framework\TestCase;

class InArrayTest extends TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_matching_values($needle, $haystack, $expectedResult)
    {
        $expander = new InArray($needle);
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    public static function examplesProvider()
    {
        return [
            ['ipsum', ['ipsum'], true],
            [1, ['foo', 1], true],
            [['foo' => 'bar'], [['foo' => 'bar']], true],
        ];
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage)
    {
        $expander = new InArray($boundary);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return [
            ['ipsum', ['ipsum lorem'], "Array(1) doesn't have \"ipsum\" element."],
            ['lorem', new \DateTime(), 'InArray expander require "array", got "\\DateTime".'],
        ];
    }
}
