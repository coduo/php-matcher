<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsEmpty;
use PHPUnit\Framework\TestCase;

class IsEmptyTest extends TestCase
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
