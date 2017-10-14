<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\Optional;
use PHPUnit\Framework\TestCase;

class OptionalTest extends TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_optional_expander_match($value, $expectedResult)
    {
        $expander = new Optional();
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    public static function examplesProvider()
    {
        return [
            [[], true],
            [['data'], true],
            ['', true],
            [0, true],
            [10.1, true],
            [null, true],
            [true, true],
            ['Lorem ipsum', true],
        ];
    }
}
