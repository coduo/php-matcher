<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsEmpty;

/**
 * @author Benjamin Lazarecki <benjamin.lazarecki@gmail.com>
 */
class IsEmptyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_is_empty_expander_match($value, $expectedResult)
    {
        $expander = new IsEmpty();
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    public static function examplesProvider()
    {
        return array(
            array(array(), true),
            array(array('data'), false),
            array('', true),
            array(0, true),
            array(null, true),
        );
    }
}
