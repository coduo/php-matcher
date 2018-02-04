<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\DoubleMatcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class DoubleMatcherTest extends TestCase
{
    /**
     * @var DoubleMatcher
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new DoubleMatcher(new Parser(new Lexer(), new Parser\ExpanderInitializer()));
    }

    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $this->assertTrue($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $this->matcher->match($value, $pattern);
        $this->assertEquals($error, $this->matcher->getError());
    }

    public static function positiveCanMatchData()
    {
        return [
            ['@double@']
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [10.1, '@double@'],
            [10.1, '@double@.lowerThan(50.12).greaterThan(10)'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@double'],
            ['double'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            ['1', '@double@'],
            [new \DateTime(),  '@double@'],
            [10,  '@double@'],
            [4.9, '@double@.greaterThan(5)'],
            [4.9, '@double@.lowerThan(20).greaterThan(5)'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  '@integer@', 'object "\\stdClass" is not a valid double.'],
            [25, '@integer@', 'integer "25" is not a valid double.'],
            [false, '@integer@', 'boolean "false" is not a valid double.'],
            [['test'], '@integer@', 'array "Array(1)" is not a valid double.']
        ];
    }
}
