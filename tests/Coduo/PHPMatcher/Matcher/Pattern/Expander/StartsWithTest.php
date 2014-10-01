<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\StartsWith;

class StartsWithTest extends \PHPUnit_Framework_TestCase
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
        return array(
            array("lorem", "lorem ipsum", true),
            array("lorem", "Lorem ipsum", false),
            array("", "lorem ipsum", true),
            array("lorem", "lorem ipsum", true),
            array("ipsum", "lorem ipsum", false)
        );
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
        return array(
            array("lorem", "Lorem ipsum", true),
            array("Lorem", "lorem ipsum", true),
            array("LOREM", "LoReM ipsum", true),
        );
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
        return array(
            array("lorem", "ipsum lorem", "string \"ipsum lorem\" doesn't starts with string \"lorem\"."),
            array("lorem", new \DateTime(), "StartsWith expander require \"string\", got \"\\DateTime\"."),
        );
    }
}
