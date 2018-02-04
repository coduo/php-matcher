<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Parser;
use Coduo\PHPMatcher\Matcher\UuidMatcher;
use PHPUnit\Framework\TestCase;

class UuidMatcherTest extends TestCase
{
    /**
     * @var UuidMatcher
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new UuidMatcher(new Parser(new Lexer(), new Parser\ExpanderInitializer()));
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
            ['@uuid@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['21627164-acb7-11e6-80f5-76304dec7eb7', '@uuid@'],
            ['d9c04bc2-173f-2cb7-ad4e-e4ca3b2c273f', '@uuid@'],
            ['7b368038-a5ca-3aa3-b0db-1177d1761c9e', '@uuid@'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae18', '@uuid@'],
            ['1f2b1a18-81a0-5685-bca7-f23022ed7c7b', '@uuid@'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@uuid'],
            ['uuid'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [1, '@uuid@'],
            [0, '@uuid@'],
            ['9f4d-b639-0e87-4367-9beb-d64e3f42ae18', '@uuid@'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae1', '@uuid@'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae181', '@uuid@'],
            ['9f4db6390e8743679bebd64e3f42ae18', '@uuid@'],
            ['9f4db6390e87-4367-9beb-d64e-3f42ae18', '@uuid@'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae1g', '@uuid@'],
            ['9f4db639-0e87-0367-9beb-d64e3f42ae18', '@uuid@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  '@uuid@', 'object "\\stdClass" is not a valid UUID: not a string.'],
            [1.1, '@uuid@', 'double "1.1" is not a valid UUID: not a string.'],
            [false, '@uuid@', 'boolean "false" is not a valid UUID: not a string.'],
            [1, '@uuid@', 'integer "1" is not a valid UUID: not a string.'],
            ['lorem ipsum', '@uuid@', 'string "lorem ipsum" is not a valid UUID: invalid format.'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae1z', '@uuid@', 'string "9f4db639-0e87-4367-9beb-d64e3f42ae1z" is not a valid UUID: invalid format.'],
        ];
    }
}
