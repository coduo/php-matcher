<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\DateTimeMatcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class DateTimeMatcherTest extends TestCase
{
    private DateTimeMatcher $matcher;

    public static function positiveCanMatchData()
    {
        return [
            ['@datetime@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['2020-01-01', '@datetime@'],
            ['2020-01-01 00:00:00', '@datetime@'],
            ['2020-01-01 00:00:00', '@datetime@.isDateTime()'],
            ['2020-01-01 01:00:00', '@datetime@.after("2020-01-01 00:00:00")'],
            ['2020-01-01 01:00:00', '@datetime@.before("2021-01-01 00:00:00")'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@datetime'],
            ['datetime'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [1, '@datetime@'],
            [0,  '@datetime@'],
            ['01:00:00', '@datetime@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  '@datetime@', 'object "\\stdClass" is not a valid string.'],
            [1.1, '@integer@', 'double "1.1" is not a valid string.'],
            [false, '@double@', 'boolean "false" is not a valid string.'],
            [1, '@array@', 'integer "1" is not a valid string.'],
            ['lorem ipsum', "@array@.startsWith('ipsum')", 'lorem ipsum "lorem ipsum" is not a valid date time.'],
        ];
    }

    public function setUp() : void
    {
        $this->matcher = new DateTimeMatcher(
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
