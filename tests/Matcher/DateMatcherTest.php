<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\DateMatcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class DateMatcherTest extends TestCase
{
    private DateMatcher $matcher;

    public static function positiveCanMatchData()
    {
        return [
            ['@date@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['2020-01-01', '@date@'],
            ['2020-01-01', '@date@'],
            ['2020-01-01', '@date@.isDateTime()'],
            ['2020-01-02', '@date@.after("2020-01-01")'],
            ['2020-01-01', '@date@.before("2021-01-03")'],
            ['-2 hours', '@date@.after("-5 hours")'],
            ['+3 hours', '@date@.before("+1 day")'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@string'],
            ['string'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [1, '@date@'],
            [0,  '@date@'],
            ['01:00',  '@date@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  '@dat@', 'object "\\stdClass" is not a valid string.'],
            [1.1, '@integer@', 'double "1.1" is not a valid string.'],
            [false, '@double@', 'boolean "false" is not a valid string.'],
            [1, '@array@', 'integer "1" is not a valid string.'],
            ['lorem ipsum', "@array@.startsWith('ipsum')", 'lorem ipsum "lorem ipsum" is not a valid date.'],
        ];
    }

    public function setUp() : void
    {
        $this->matcher = new DateMatcher(
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
