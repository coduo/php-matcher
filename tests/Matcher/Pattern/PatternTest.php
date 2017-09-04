<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsEmail;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsEmpty;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\Optional;
use Coduo\PHPMatcher\Matcher\Pattern\Pattern;
use Coduo\PHPMatcher\Matcher\Pattern\TypePattern;

class PatternTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pattern
     */
    private $pattern;

    public function setUp()
    {
        $this->pattern = new TypePattern('dummy');
        $this->pattern->addExpander(new isEmail());
        $this->pattern->addExpander(new isEmpty());
        $this->pattern->addExpander(new Optional());
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_has_expander($expander, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->pattern->hasExpander($expander));
    }

    public static function examplesProvider()
    {
        return array(
            array("isEmail", true),
            array("isEmpty", true),
            array("optional", true),
            array("isUrl", false),
            array("non existing expander", false),
        );
    }
}
