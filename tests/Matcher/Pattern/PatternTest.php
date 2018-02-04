<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsEmail;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\IsEmpty;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\Optional;
use Coduo\PHPMatcher\Matcher\Pattern\Pattern;
use Coduo\PHPMatcher\Matcher\Pattern\TypePattern;
use PHPUnit\Framework\TestCase;

class PatternTest extends TestCase
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
        return [
            ['isEmail', true],
            ['isEmpty', true],
            ['optional', true],
            ['isUrl', false],
            ['non existing expander', false],
        ];
    }
}
