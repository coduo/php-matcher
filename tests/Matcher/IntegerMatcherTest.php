<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\IntegerMatcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class IntegerMatcherTest extends TestCase
{
    /**
     * @var IntegerMatcher
     */
    private $matcher;

    public static function positiveCanMatchData()
    {
        return [
            ['@integer@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [10, '@integer@'],
            [10, '@integer@.lowerThan(50).greaterThan(1)'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@integer'],
            ['integer'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            ['1', '@integer@'],
            [new \DateTime(),  '@integer@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  '@integer@', 'object "\\stdClass" is not a valid integer.'],
            [1.1, '@integer@', 'double "1.1" is not a valid integer.'],
            [false, '@integer@', 'boolean "false" is not a valid integer.'],
            [['test'], '@integer@', 'array "Array(1)" is not a valid integer.'],
        ];
    }

    public function setUp() : void
    {
        $this->matcher = new IntegerMatcher(
            $backtrace = new Backtrace\InMemoryBacktrace(),
            new Parser(new Lexer(), new Parser\ExpanderInitializer($backtrace))
        );
    }

    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern) : void
    {
        $this->assertTrue($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern) : void
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern) : void
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern) : void
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error) : void
    {
        $this->matcher->match($value, $pattern);
        $this->assertEquals($error, $this->matcher->getError());
    }
}
