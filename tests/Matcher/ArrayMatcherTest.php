<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class ArrayMatcherTest extends TestCase
{
    /**
     * @var Matcher\ArrayMatcher
     */
    private $matcher;

    public static function positiveMatchData()
    {
        $simpleArr = [
            'users' => [
                [
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz',
                ],
                [
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski',
                ],
            ],
            true,
            false,
            1,
            6.66,
        ];

        $simpleArrPattern = [
            'users' => [
                [
                    'firstName' => '@string@',
                    'lastName' => '@string@',
                ],
                Matcher\ArrayMatcher::UNBOUNDED_PATTERN,
            ],
            true,
            false,
            1,
            6.66,
        ];

        $simpleArrPatternWithUniversalKey = [
            'users' => [
                [
                    'firstName' => '@string@',
                    Matcher\ArrayMatcher::UNIVERSAL_KEY => '@*@',
                ],
                Matcher\ArrayMatcher::UNBOUNDED_PATTERN,
            ],
            true,
            false,
            1,
            6.66,
        ];

        $simpleArrPatternWithUniversalKeyAndStringValue = [
            'users' => [
                [
                    'firstName' => '@string@',
                    Matcher\ArrayMatcher::UNIVERSAL_KEY => '@string@',
                ],
                Matcher\ArrayMatcher::UNBOUNDED_PATTERN,
            ],
            true,
            false,
            1,
            6.66,
        ];

        return [
            [$simpleArr, $simpleArr],
            [$simpleArr, $simpleArrPattern],
            [$simpleArr, $simpleArrPatternWithUniversalKey],
            [$simpleArr, $simpleArrPatternWithUniversalKeyAndStringValue],
            [[], []],
            [[], ['@boolean@.optional()']],
            [['foo' => null], ['foo' => null]],
            [['foo' => null], ['foo' => '@null@']],
            [['key' => 'val'], ['key' => 'val']],
            [[1], [1]],
            [
                ['roles' => ['ROLE_ADMIN', 'ROLE_DEVELOPER']],
                ['roles' => '@wildcard@'],
            ],
            'unbound array should match one or none elements' => [
                [
                    'users' => [
                        [
                            'firstName' => 'Norbert',
                            'lastName' => 'Foobar',
                        ],
                    ],
                    true,
                    false,
                    1,
                    6.66,
                ],
                $simpleArrPattern,
            ],
            [['foo[key]' => 'value'], ['foo[key]' => '@string@']],
        ];
    }

    public static function negativeMatchData()
    {
        $simpleArr = [
            'users' => [
                [
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz',
                ],
                [
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski',
                ],
            ],
            true,
            false,
            1,
            6.66,
        ];

        $simpleDiff = [
            'users' => [
                [
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz',
                ],
                [
                    'firstName' => 'Pablo',
                    'lastName' => 'Dąbrowski',
                ],
            ],
            true,
            false,
            1,
            6.66,
        ];

        $simpleArrPatternWithUniversalKeyAndIntegerValue = [
            'users' => [
                [
                    'firstName' => '@string@',
                    Matcher\ArrayMatcher::UNIVERSAL_KEY => '@integer@',
                ],
                Matcher\ArrayMatcher::UNBOUNDED_PATTERN,
            ],
            true,
            false,
            1,
            6.66,
        ];

        return [
            [$simpleArr, $simpleDiff],
            [$simpleArr, $simpleArrPatternWithUniversalKeyAndIntegerValue],
            [['status' => 'ok', 'data' => [['foo']]], ['status' => 'ok', 'data' => []]],
            [[1], []],
            [[], ['key' => []]],
            [['key' => 'val'], ['key' => 'val2']],
            [[1], [2]],
            [['foo', 1, 3], ['foo', 2, 3]],
            [[], ['key' => []]],
            [[], ['foo' => 'bar']],
            [[], ['foo' => ['bar' => []]]],
            [['key' => 'val', 'key2' => 'val2'], ['not key' => 'val', '@*@' => '@*@']],
            'unbound array should match one or none elements' => [
                [
                    'users' => [
                        [
                            'firstName' => 'Norbert',
                            'lastName' => 'Foobar',
                        ],
                    ],
                    true,
                    false,
                    1,
                    6.66,
                ],
                $simpleDiff,
            ],
        ];
    }

    public function setUp() : void
    {
        $backtrace = new Backtrace\InMemoryBacktrace();
        $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer($backtrace));

        $matchers = [
            new Matcher\CallbackMatcher($backtrace),
            new Matcher\NullMatcher($backtrace),
            new Matcher\StringMatcher($backtrace, $parser),
            new Matcher\IntegerMatcher($backtrace, $parser),
            new Matcher\BooleanMatcher($backtrace, $parser),
            new Matcher\DoubleMatcher($backtrace, $parser),
            new Matcher\NumberMatcher($backtrace, $parser),
            new Matcher\DateMatcher($backtrace, $parser),
            new Matcher\ScalarMatcher($backtrace),
            new Matcher\WildcardMatcher($backtrace),
        ];

        if (\class_exists('Symfony\\Component\\ExpressionLanguage\\ExpressionLanguage')) {
            $matchers[] = new Matcher\ExpressionMatcher($backtrace);
        }

        $this->matcher = new Matcher\ArrayMatcher(
            new Matcher\ChainMatcher(self::class, $backtrace, $matchers),
            $backtrace,
            $parser
        );
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match_arrays($value, $pattern) : void
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match_arrays($value, $pattern) : void
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
    }

    public function test_negative_match_when_cant_find_matcher_that_can_match_array_element() : void
    {
        $matcher = new Matcher\ArrayMatcher(
            new Matcher\ChainMatcher(
                self::class,
                $backtrace = new Backtrace\InMemoryBacktrace(),
                [
                    new Matcher\WildcardMatcher($backtrace),
                ]
            ),
            $backtrace,
            $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer($backtrace))
        );

        $this->assertTrue($matcher->match(['test' => 1], ['test' => 1]));
    }

    public function test_error_when_path_in_pattern_does_not_exist() : void
    {
        $this->assertFalse($this->matcher->match(['foo' => 'foo value'], ['bar' => 'bar value']));
        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo] in pattern.');
    }

    public function test_error_when_path_in_nested_pattern_does_not_exist() : void
    {
        $array = ['foo' => ['bar' => ['baz' => 'bar value']]];
        $pattern = ['foo' => ['bar' => ['faz' => 'faz value']]];

        $this->assertFalse($this->matcher->match($array, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][baz] in pattern.');
    }

    public function test_error_when_path_in_value_does_not_exist() : void
    {
        $array = ['foo' => 'foo'];
        $pattern = ['foo' => 'foo', 'bar' => 'bar'];

        $this->assertFalse($this->matcher->match($array, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [bar] in value.');
    }

    public function test_error_when_path_in_nested_value_does_not_exist() : void
    {
        $array = ['foo' => ['bar' => []]];
        $pattern = ['foo' => ['bar' => ['faz' => 'faz value']]];

        $this->assertFalse($this->matcher->match($array, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][faz] in value.');
    }

    public function test_error_when_matching_fail() : void
    {
        $this->assertFalse($this->matcher->match(['foo' => 'foo value'], ['foo' => 'bar value']));
        $this->assertEquals($this->matcher->getError(), '"foo value" does not match "bar value".');
    }

    public function test_error_message_when_matching_non_array_value() : void
    {
        $this->assertFalse($this->matcher->match(new \DateTime(), '@array@'));
        $this->assertEquals($this->matcher->getError(), 'object "\\DateTime" is not a valid array.');
    }

    public function test_matching_array_to_array_pattern() : void
    {
        $this->assertTrue($this->matcher->match(['foo', 'bar'], '@array@'));
        $this->assertTrue($this->matcher->match(['foo'], '@array@.inArray("foo")'));
        $this->assertTrue($this->matcher->match(
            ['foo', ['bar']],
            [
                '@string@',
                '@array@.inArray("bar")',
            ]
        ));
    }
}
