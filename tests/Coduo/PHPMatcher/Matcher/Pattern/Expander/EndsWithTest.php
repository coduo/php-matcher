<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\EndsWith;

class EndsWithTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider notIgnoringCaseExamplesProvider
     */
    public function test_examples_not_ignoring_case($stringEnding, $value, $expectedResult)
    {
        $expander = new EndsWith($stringEnding);
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    public static function notIgnoringCaseExamplesProvider()
    {
        return array(
            array("ipsum", "lorem ipsum", true),
            array("ipsum", "Lorem IPSUM", false),
            array("", "lorem ipsum", true),
            array("ipsum", "lorem ipsum", true),
            array("lorem", "lorem ipsum", false)
        );
    }

    /**
     * @dataProvider ignoringCaseExamplesProvider
     */
    public function test_examples_ignoring_case($stringEnding, $value, $expectedResult)
    {
        $expander = new EndsWith($stringEnding, true);
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    public static function ignoringCaseExamplesProvider()
    {
        return array(
            array("Ipsum", "Lorem ipsum", true),
            array("iPsUm", "lorem ipsum", true),
            array("IPSUM", "LoReM ipsum", true),
        );
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($stringBeginning, $value, $errorMessage)
    {
        $expander = new EndsWith($stringBeginning);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return array(
            array("ipsum", "ipsum lorem", "string \"ipsum lorem\" doesn't ends with string \"ipsum\"."),
            array("lorem", new \DateTime(), "EndsWith expander require \"string\", got \"\\DateTime\"."),
        );
    }
}
