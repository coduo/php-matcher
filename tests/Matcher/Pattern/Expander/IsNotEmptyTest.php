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
        return array(
            array("lorem", true),
            array("0", true),
            array(new \DateTime(), true),
            array("", false),
            array(null, false),
            array(array(), false)
        );
    }
}
