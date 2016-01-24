<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsNotEmpty;

class IsNotEmptyTest extends \PHPUnit_Framework_TestCase
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
