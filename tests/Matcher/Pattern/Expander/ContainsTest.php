<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\Contains;
use PHPUnit\Framework\TestCase;

class ContainsTest extends TestCase
{
    public static function examplesCaseSensitiveProvider()
    {
        return [
            ['ipsum', 'lorem ipsum', true],
            ['wor', 'this is my hello world string', true],
            ['lol', 'lorem ipsum', false],
            ['NO', 'norbert', false],
        ];
    }

    public static function examplesCaseInsensitiveProvider()
    {
        return [
            ['IpSum', 'lorem ipsum', true],
            ['wor', 'this is my hello WORLD string', true],
            ['lol', 'LOREM ipsum', false],
            ['NO', 'NORBERT', true],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            ['ipsum', 'hello world', "String \"hello world\" doesn't contains \"ipsum\"."],
            ['lorem', new \DateTime(), 'Contains expander require "string", got "\\DateTime".'],
        ];
    }

    /**
     * @dataProvider examplesCaseSensitiveProvider
     */
    public function test_matching_values_case_sensitive($needle, $haystack, $expectedResult) : void
    {
        $expander = new Contains($needle);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    /**
     * @dataProvider examplesCaseInsensitiveProvider
     */
    public function test_matching_values_case_insensitive($needle, $haystack, $expectedResult) : void
    {
        $expander = new Contains($needle, true);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($string, $value, $errorMessage) : void
    {
        $expander = new Contains($string);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
