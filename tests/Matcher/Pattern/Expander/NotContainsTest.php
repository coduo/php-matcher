<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\NotContains;
use PHPUnit\Framework\TestCase;

class NotContainsTest extends TestCase
{
    public static function examplesCaseSensitiveProvider()
    {
        return [
            ['ipsum', 'lorem ipsum', false],
            ['wor', 'this is my hello world string', false],
            ['lol', 'lorem ipsum', true],
            ['NO', 'norbert', true],
        ];
    }

    public static function examplesCaseInsensitiveProvider()
    {
        return [
            ['IpSum', 'lorem ipsum', false],
            ['wor', 'this is my hello WORLD string', false],
            ['lol', 'LOREM ipsum', true],
            ['NO', 'NORBERT', false],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            ['ipsum', 'lorem ipsum', 'String "lorem ipsum" contains "ipsum".'],
            ['lorem', new \DateTime(), 'Not contains expander require "string", got "\\DateTime".'],
        ];
    }

    /**
     * @dataProvider examplesCaseSensitiveProvider
     */
    public function test_matching_values_case_sensitive($needle, $haystack, $expectedResult) : void
    {
        $expander = new NotContains($needle);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    /**
     * @dataProvider examplesCaseInsensitiveProvider
     */
    public function test_matching_values_case_insensitive($needle, $haystack, $expectedResult) : void
    {
        $expander = new NotContains($needle, true);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($string, $value, $errorMessage) : void
    {
        $expander = new NotContains($string);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
