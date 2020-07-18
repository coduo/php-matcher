<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\EndsWith;
use PHPUnit\Framework\TestCase;

class EndsWithTest extends TestCase
{
    public static function notIgnoringCaseExamplesProvider()
    {
        return [
            ['ipsum', 'lorem ipsum', true],
            ['ipsum', 'Lorem IPSUM', false],
            ['', 'lorem ipsum', true],
            ['ipsum', 'lorem ipsum', true],
            ['lorem', 'lorem ipsum', false],
        ];
    }

    public static function ignoringCaseExamplesProvider()
    {
        return [
            ['Ipsum', 'Lorem ipsum', true],
            ['iPsUm', 'lorem ipsum', true],
            ['IPSUM', 'LoReM ipsum', true],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            ['ipsum', 'ipsum lorem', "string \"ipsum lorem\" doesn't ends with string \"ipsum\"."],
            ['lorem', new \DateTime(), 'EndsWith expander require "string", got "\\DateTime".'],
        ];
    }

    /**
     * @dataProvider notIgnoringCaseExamplesProvider
     */
    public function test_examples_not_ignoring_case($stringEnding, $value, $expectedResult) : void
    {
        $expander = new EndsWith($stringEnding);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    /**
     * @dataProvider ignoringCaseExamplesProvider
     */
    public function test_examples_ignoring_case($stringEnding, $value, $expectedResult) : void
    {
        $expander = new EndsWith($stringEnding, true);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($stringBeginning, $value, $errorMessage) : void
    {
        $expander = new EndsWith($stringBeginning);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
