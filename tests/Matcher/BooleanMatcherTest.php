<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\BooleanMatcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class BooleanMatcherTest extends TestCase
{
    private BooleanMatcher $matcher;

    private Backtrace $backtrace;

    public static function positiveCanMatchData()
    {
        return [
            ['@boolean@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [true, '@boolean@'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@boolean'],
            ['boolean'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            ['1', '@boolean@'],
            [new \DateTime(),  '@boolean@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  '@boolean@', 'object "\\stdClass" is not a valid boolean.'],
            [1.1, '@boolean@', 'double "1.1" is not a valid boolean.'],
            ['true', '@string@', 'string "true" is not a valid boolean.'],
            [['test'], '@boolean@', 'array "Array(1)" is not a valid boolean.'],
        ];
    }

    public function setUp() : void
    {
        $this->matcher = new BooleanMatcher(
            $this->backtrace = new Backtrace\InMemoryBacktrace(),
            new Parser(new Lexer(), new Parser\ExpanderInitializer($this->backtrace))
        );
    }

    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern) : void
    {
        $this->assertTrue($this->matcher->canMatch($pattern));
        $this->assertFalse($this->backtrace->isEmpty());
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern) : void
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
        $this->assertFalse($this->backtrace->isEmpty());
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern) : void
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
        $this->assertFalse($this->backtrace->isEmpty());
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern) : void
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
        $this->assertFalse($this->backtrace->isEmpty());
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error) : void
    {
        $this->matcher->match($value, $pattern);
        $this->assertEquals($error, $this->matcher->getError());
        $this->assertFalse($this->backtrace->isEmpty());
    }
}
