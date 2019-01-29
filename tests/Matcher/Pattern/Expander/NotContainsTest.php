<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\NotContains;
use PHPUnit\Framework\TestCase;

class NotContainsTest extends TestCase
{
    /**
     * @dataProvider examplesCaseSensitiveProvider
     */
    public function test_matching_values_case_sensitive($needle, $haystack, $expectedResult)
    {
        $expander = new NotContains($needle);
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    public static function examplesCaseSensitiveProvider()
    {
        return [
            ['ipsum', 'lorem ipsum', false],
            ['wor', 'this is my hello world string', false],
            ['lol', 'lorem ipsum', true],
            ['NO', 'norbert', true]
        ];
    }

    /**
     * @dataProvider examplesCaseInsensitiveProvider
     */
    public function test_matching_values_case_insensitive($needle, $haystack, $expectedResult)
    {
        $expander = new NotContains($needle, true);
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    public static function examplesCaseInsensitiveProvider()
    {
        return [
            ['IpSum', 'lorem ipsum', false],
            ['wor', 'this is my hello WORLD string', false],
            ['lol', 'LOREM ipsum', true],
            ['NO', 'NORBERT', false]
        ];
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($string, $value, $errorMessage)
    {
        $expander = new NotContains($string);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return [
            ['ipsum', 'lorem ipsum', 'String "lorem ipsum" contains "ipsum".'],
            ['lorem', new \DateTime(), 'Not contains expander require "string", got "\\DateTime".'],
        ];
    }
}
