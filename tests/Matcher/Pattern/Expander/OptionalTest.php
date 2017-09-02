<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\Optional;

class OptionalTest extends \PHPUnit_Framework_TestCase
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
        return array(
            array(array(), true),
            array(array('data'), true),
            array('', true),
            array(0, true),
            array(10.1, true),
            array(null, true),
            array('Lorem ipsum', true),
        );
    }
}
