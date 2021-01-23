<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\JsonObjectMatcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

final class JsonObjectMatcherTest extends TestCase
{
    private ?\Coduo\PHPMatcher\Matcher\JsonObjectMatcher $matcher = null;

    public static function positiveMatches()
    {
        return [
            [
                '{"users":["Norbert","Michał"]}',
                '@json@',
            ],
            [
                null,
                '@json@.optional()',
            ],
            [
                '{"users":["Norbert","Michał"]}',
                '@json@.contains("users")',
            ],
            [
                '{"users":["Norbert","Michał"]}',
                '@json@.hasProperty("users")',
            ],
            [
                [1, 2, 3],
                '@json@',
            ],
        ];
    }

    public static function negativeMatches()
    {
        return [
            [
                '{"users":["Norbert","Michał"]',
                '@json@',
                'Invalid given JSON of value. Syntax error, malformed JSON',
            ],
            [
                1,
                '@json@',
                'Invalid given JSON of value. Syntax error, malformed JSON',
            ],
        ];
    }

    public function setUp() : void
    {
        $this->matcher = new JsonObjectMatcher(
            $backtrace = new Backtrace\InMemoryBacktrace(),
            new Parser(new Lexer(), new Parser\ExpanderInitializer($backtrace))
        );
    }

    /**
     * @dataProvider positiveMatches
     */
    public function test_matching_valid_json_string($jsonString, $pattern) : void
    {
        $this->assertTrue($this->matcher->match($jsonString, $pattern), (string) $this->matcher->getError());
    }

    /**
     * @dataProvider negativeMatches
     */
    public function test_matching_invalid_json_string($jsonString, string $pattern, string $expectedError) : void
    {
        $this->assertFalse($this->matcher->match($jsonString, $pattern), (string) $this->matcher->getError());
        $this->assertSame($expectedError, $this->matcher->getError());
    }
}
