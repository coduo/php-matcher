<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\StringMatcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class StringMatcherTest extends TestCase
{
    /**
     * @var StringMatcher
     */
    private $matcher;

    public function setUp()
    {
        $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer());
        $this->matcher = new StringMatcher($parser);
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
            ['@string@']
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['lorem ipsum', '@string@'],
            ['lorem ipsum', '@string@.isNotEmpty()'],
            ['lorem ipsum', "@string@.startsWith('lorem')"],
            ['lorem ipsum', "@string@.endsWith('ipsum')"],
            ['lorem ipsum dolor', "@string@.startsWith('lorem').contains('ipsum').endsWith('dolor')"],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@string'],
            ['string'],
            [1]
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [1, '@string@'],
            [0,  '@string@']
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  '@string@', 'object "\\stdClass" is not a valid string.'],
            [1.1, '@integer@', 'double "1.1" is not a valid string.'],
            [false, '@double@', 'boolean "false" is not a valid string.'],
            [1, '@array@', 'integer "1" is not a valid string.'],
            ['lorem ipsum', "@array@.startsWith('ipsum')", "string \"lorem ipsum\" doesn't starts with string \"ipsum\"."]
        ];
    }
}
