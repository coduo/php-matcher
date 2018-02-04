<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsNotEmpty;
use PHPUnit\Framework\TestCase;

class IsNotEmptyTest extends TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_examples_not_ignoring_case($value, $expectedResult)
    {
        $expander = new IsNotEmpty();
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    public static function examplesProvider()
    {
        return [
            ['lorem', true],
            ['0', true],
            [new \DateTime(), true],
            ['', false],
            [null, false],
            [[], false]
        ];
    }
}
