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
    private Matcher\ArrayMatcher $matcher;

    private Backtrace $backtrace;

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
        $this->backtrace = new Backtrace\InMemoryBacktrace();
        $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer($this->backtrace));

        $matchers = [
            new Matcher\CallbackMatcher($this->backtrace),
            new Matcher\NullMatcher($this->backtrace),
            new Matcher\StringMatcher($this->backtrace, $parser),
            new Matcher\IntegerMatcher($this->backtrace, $parser),
            new Matcher\BooleanMatcher($this->backtrace, $parser),
            new Matcher\DoubleMatcher($this->backtrace, $parser),
            new Matcher\NumberMatcher($this->backtrace, $parser),
            new Matcher\DateMatcher($this->backtrace, $parser),
            new Matcher\ScalarMatcher($this->backtrace),
            new Matcher\WildcardMatcher($this->backtrace),
        ];

        if (\class_exists('Symfony\\Component\\ExpressionLanguage\\ExpressionLanguage')) {
            $matchers[] = new Matcher\ExpressionMatcher($this->backtrace);
        }

        $this->matcher = new Matcher\ArrayMatcher(
            new Matcher\ChainMatcher(self::class, $this->backtrace, $matchers),
            $this->backtrace,
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
        $this->assertFalse($backtrace->isEmpty());
    }

    public function test_error_when_path_in_pattern_does_not_exist() : void
    {
        $this->assertFalse($this->matcher->match(['foo' => 'foo value'], ['bar' => 'bar value']));
        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo] in pattern.');
        $this->assertFalse($this->backtrace->isEmpty());
    }

    public function test_error_when_path_in_nested_pattern_does_not_exist() : void
    {
        $array = ['foo' => ['bar' => ['baz' => 'bar value']]];
        $pattern = ['foo' => ['bar' => ['faz' => 'faz value']]];

        $this->assertFalse($this->matcher->match($array, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][baz] in pattern.');
        $this->assertFalse($this->backtrace->isEmpty());
    }

    public function test_error_when_path_in_value_does_not_exist() : void
    {
        $array = ['foo' => 'foo'];
        $pattern = ['foo' => 'foo', 'bar' => 'bar'];

        $this->assertFalse($this->matcher->match($array, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [bar] in value.');
        $this->assertFalse($this->backtrace->isEmpty());
    }

    public function test_error_when_path_in_nested_value_does_not_exist() : void
    {
        $array = ['foo' => ['bar' => []]];
        $pattern = ['foo' => ['bar' => ['faz' => 'faz value']]];

        $this->assertFalse($this->matcher->match($array, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][faz] in value.');
        $this->assertFalse($this->backtrace->isEmpty());
    }

    public function test_error_when_matching_fail() : void
    {
        $this->assertFalse($this->matcher->match(['foo' => 'foo value'], ['foo' => 'bar value']));
        $this->assertEquals($this->matcher->getError(), '"foo value" does not match "bar value".');
        $this->assertFalse($this->backtrace->isEmpty());
    }

    public function test_error_message_when_matching_non_array_value() : void
    {
        $this->assertFalse($this->matcher->match(new \DateTime(), '@array@'));
        $this->assertEquals($this->matcher->getError(), 'object "\\DateTime" is not a valid array.');
        $this->assertFalse($this->backtrace->isEmpty());
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
        $this->assertFalse($this->backtrace->isEmpty());
    }

    public function test_array_previous_pattern() : void
    {
        $this->assertTrue(
            $this->matcher->match(
                [
                    ['id' => 1, 'unique' => \uniqid()],
                    ['id' => 2, 'unique' => \uniqid()],
                    ['id' => 3, 'unique' => \uniqid()],
                    ['id' => 4, 'unique' => \uniqid()],
                    ['id' => '5', 'unique' => \uniqid()],
                    ['id' => '6', 'unique' => \uniqid()],
                    ['id' => '7', 'unique' => \uniqid()],
                ],
                [
                    ['id' => '@integer@', 'unique' => '@string@'],
                    '@array_previous@',
                    '@array_previous@',
                    '@array_previous@',
                    ['id' => '@string@', 'unique' => '@string@'],
                    '@array_previous@',
                    '@array_previous@',
                ]
            ),
            $this->matcher->getError()
        );
    }
}
