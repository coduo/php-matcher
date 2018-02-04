<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Parser;
use Coduo\PHPMatcher\Matcher\NullMatcher;
use PHPUnit\Framework\TestCase;

class NullMatcherTest extends TestCase
{
    /**
     * @var NullMatcher
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new NullMatcher(new Parser(new Lexer(), new Parser\ExpanderInitializer()));
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
            ['@null@'],
            [null]
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [null, '@null@'],
            [null, null],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@null'],
            ['null'],
            [0]
        ];
    }

    public static function negativeMatchData()
    {
        return [
            ['null', '@null@'],
            [0,  '@null@']
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            ['test', '@null@', 'string "test" does not match null.'],
            [new \stdClass,  '@null@', 'object "\\stdClass" does not match null.'],
            [1.1, '@null@', 'double "1.1" does not match null.'],
            [false, '@null@', 'boolean "false" does not match null.'],
            [1, '@null@', 'integer "1" does not match null.']
        ];
    }
}
