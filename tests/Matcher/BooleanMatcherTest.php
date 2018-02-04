<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Parser;
use Coduo\PHPMatcher\Matcher\BooleanMatcher;
use PHPUnit\Framework\TestCase;

class BooleanMatcherTest extends TestCase
{
    /**
     * @var BooleanMatcher
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new BooleanMatcher(new Parser(new Lexer(), new Parser\ExpanderInitializer()));
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
            ['@boolean@']
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
            [1]
        ];
    }

    public static function negativeMatchData()
    {
        return [
            ['1', '@boolean@'],
            [new \DateTime(),  '@boolean@']
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  '@boolean@', 'object "\\stdClass" is not a valid boolean.'],
            [1.1, '@boolean@', 'double "1.1" is not a valid boolean.'],
            ['true', '@string@', 'string "true" is not a valid boolean.'],
            [['test'], '@boolean@', 'array "Array(1)" is not a valid boolean.']
        ];
    }
}
