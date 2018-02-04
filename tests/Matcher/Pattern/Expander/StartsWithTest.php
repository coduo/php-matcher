<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\StartsWith;
use PHPUnit\Framework\TestCase;

class StartsWithTest extends TestCase
{
    /**
     * @dataProvider notIgnoringCaseExamplesProvider
     */
    public function test_examples_not_ignoring_case($stringBeginning, $value, $expectedResult)
    {
        $expander = new StartsWith($stringBeginning);
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    public static function notIgnoringCaseExamplesProvider()
    {
        return [
            ['lorem', 'lorem ipsum', true],
            ['lorem', 'Lorem ipsum', false],
            ['', 'lorem ipsum', true],
            ['lorem', 'lorem ipsum', true],
            ['ipsum', 'lorem ipsum', false]
        ];
    }

    /**
     * @dataProvider ignoringCaseExamplesProvider
     */
    public function test_examples_ignoring_case($stringBeginning, $value, $expectedResult)
    {
        $expander = new StartsWith($stringBeginning, true);
        $this->assertTrue($expander->match($value));
    }

    public static function ignoringCaseExamplesProvider()
    {
        return [
            ['lorem', 'Lorem ipsum', true],
            ['Lorem', 'lorem ipsum', true],
            ['LOREM', 'LoReM ipsum', true],
        ];
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($stringBeginning, $value, $errorMessage)
    {
        $expander = new StartsWith($stringBeginning);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return [
            ['lorem', 'ipsum lorem', "string \"ipsum lorem\" doesn't starts with string \"lorem\"."],
            ['lorem', new \DateTime(), 'StartsWith expander require "string", got "\\DateTime".'],
        ];
    }
}
