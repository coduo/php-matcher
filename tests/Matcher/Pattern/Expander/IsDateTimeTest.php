<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsDateTime;
use PHPUnit\Framework\TestCase;

class IsDateTimeTest extends TestCase
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
