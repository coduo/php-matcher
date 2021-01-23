<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\TimeZoneMatcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class TimeZoneMatcherTest extends TestCase
{
    private TimeZoneMatcher $matcher;

    public static function positiveCanMatchData()
    {
        return [
            ['@timezone@'],
            ['@tz@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['Europe/Warsaw', '@tz@'],
            ['Europe/Warsaw', '@timezone@'],
            ['GMT', '@timezone@'],
            ['UTC', '@timezone@'],
            ['+00:00', '@timezone@'],
            ['01:00', '@timezone@'],
            ['01:00', '@timezone@.isTzOffset()'],
            ['GMT', '@timezone@.isTzAbbreviation()'],
            ['Europe/Warsaw', '@timezone@.isTzIdentifier()'],
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
            [1, '@timezone@'],
            [0,  '@timezone@'],
            ['blablabla',  '@timezone@'],
            ['GMT', '@timezone@.isTzOffset()'],
            ['GMT', '@timezone@.isTzIdentifier()'],
            ['00:00', '@timezone@.isTzAbbreviation()'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  '@dat@', 'object "\\stdClass" is not a valid string.'],
            [1.1, '@integer@', 'double "1.1" is not a valid string.'],
            [false, '@double@', 'boolean "false" is not a valid string.'],
            [1, '@array@', 'integer "1" is not a valid string.'],
            ['lorem ipsum', "@array@.startsWith('ipsum')", 'lorem ipsum "lorem ipsum" is not a valid timezone.'],
        ];
    }

    public function setUp() : void
    {
        $this->matcher = new TimeZoneMatcher(
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
