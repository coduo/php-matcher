<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsDateTime;

class IsDateTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider examplesDatesProvider
     */
    public function test_dates($date, $expectedResult)
    {
        $expander = new IsDateTime();
        $this->assertEquals($expectedResult, $expander->match($date));
    }

    public static function examplesDatesProvider()
    {
        return array(
            array("201-20-44", false),
            array("2012-10-11", true),
            array("invalid", false),
            array("Monday, 15-Aug-2005 15:52:01 UTC", true)
        );
    }
}
