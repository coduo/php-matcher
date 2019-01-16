<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

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

    public function setUp()
    {
        $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer());
        $this->matcher = new Matcher\ArrayMatcher(
            new Matcher\ChainMatcher([
                new Matcher\CallbackMatcher(),
                new Matcher\ExpressionMatcher(),
                new Matcher\NullMatcher(),
                new Matcher\StringMatcher($parser),
                new Matcher\IntegerMatcher($parser),
                new Matcher\BooleanMatcher($parser),
                new Matcher\DoubleMatcher($parser),
                new Matcher\NumberMatcher($parser),
                new Matcher\ScalarMatcher(),
                new Matcher\WildcardMatcher(),
            ]),
            $parser
        );
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match_arrays($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match_arrays($value, $pattern)
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
    }

    public function test_negative_match_when_cant_find_matcher_that_can_match_array_element()
    {
        $matcher = new Matcher\ArrayMatcher(
            new Matcher\ChainMatcher([
                new Matcher\WildcardMatcher()
            ]),
            $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer())
        );

        $this->assertTrue($matcher->match(['test' => 1], ['test' => 1]));
    }

    public function test_error_when_path_in_pattern_does_not_exist()
    {
        $this->assertFalse($this->matcher->match(['foo' => 'foo value'], ['bar' => 'bar value']));
        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo] in pattern.');
    }

    public function test_error_when_path_in_nested_pattern_does_not_exist()
    {
        $array = ['foo' => ['bar' => ['baz' => 'bar value']]];
        $pattern = ['foo' => ['bar' => ['faz' => 'faz value']]];

        $this->assertFalse($this->matcher->match($array, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][baz] in pattern.');
    }

    public function test_error_when_path_in_value_does_not_exist()
    {
        $array = ['foo' => 'foo'];
        $pattern = ['foo' => 'foo', 'bar' => 'bar'];

        $this->assertFalse($this->matcher->match($array, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [bar] in value.');
    }

    public function test_error_when_path_in_nested_value_does_not_exist()
    {
        $array = ['foo' => ['bar' => []]];
        $pattern = ['foo' => ['bar' => ['faz' => 'faz value']]];

        $this->assertFalse($this->matcher->match($array, $pattern));

        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][faz] in value.');
    }

    public function test_error_when_matching_fail()
    {
        $this->assertFalse($this->matcher->match(['foo' => 'foo value'], ['foo' => 'bar value']));
        $this->assertEquals($this->matcher->getError(), '"foo value" does not match "bar value".');
    }

    public function test_error_message_when_matching_non_array_value()
    {
        $this->assertFalse($this->matcher->match(new \DateTime(), '@array@'));
        $this->assertEquals($this->matcher->getError(), 'object "\\DateTime" is not a valid array.');
    }

    public function test_matching_array_to_array_pattern()
    {
        $this->assertTrue($this->matcher->match(['foo', 'bar'], '@array@'));
        $this->assertTrue($this->matcher->match(['foo'], '@array@.inArray("foo")'));
        $this->assertTrue($this->matcher->match(
            ['foo', ['bar']],
            [
                '@string@',
                '@array@.inArray("bar")'
            ]
        ));
    }

    public static function positiveMatchData()
    {
        $simpleArr = [
            'users' => [
                [
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ],
                [
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski'
                ]
            ],
            true,
            false,
            1,
            6.66
        ];

        $simpleArrPattern = [
            'users' => [
                [
                    'firstName' => '@string@',
                    'lastName' => '@string@'
                ],
                Matcher\ArrayMatcher::UNBOUNDED_PATTERN
            ],
            true,
            false,
            1,
            6.66
        ];

        $simpleArrPatternWithUniversalKey = [
            'users' => [
                [
                    'firstName' => '@string@',
                    Matcher\ArrayMatcher::UNIVERSAL_KEY => '@*@'
                ],
                Matcher\ArrayMatcher::UNBOUNDED_PATTERN
            ],
            true,
            false,
            1,
            6.66
        ];

        $simpleArrPatternWithUniversalKeyAndStringValue = [
            'users' => [
                [
                    'firstName' => '@string@',
                    Matcher\ArrayMatcher::UNIVERSAL_KEY => '@string@'
                ],
                Matcher\ArrayMatcher::UNBOUNDED_PATTERN
            ],
            true,
            false,
            1,
            6.66
        ];

        return [
            [$simpleArr, $simpleArr],
            [$simpleArr, $simpleArrPattern],
            [$simpleArr, $simpleArrPatternWithUniversalKey],
            [$simpleArr, $simpleArrPatternWithUniversalKeyAndStringValue],
            [[], []],
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
        ];
    }

    public static function negativeMatchData()
    {
        $simpleArr = [
            'users' => [
                [
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ],
                [
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski'
                ]
            ],
            true,
            false,
            1,
            6.66
        ];

        $simpleDiff = [
            'users' => [
                [
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ],
                [
                    'firstName' => 'Pablo',
                    'lastName' => 'Dąbrowski'
                ]
            ],
            true,
            false,
            1,
            6.66
        ];

        $simpleArrPatternWithUniversalKeyAndIntegerValue = [
            'users' => [
                [
                    'firstName' => '@string@',
                    Matcher\ArrayMatcher::UNIVERSAL_KEY => '@integer@'
                ],
                Matcher\ArrayMatcher::UNBOUNDED_PATTERN
            ],
            true,
            false,
            1,
            6.66
        ];

        return [
            [$simpleArr, $simpleDiff],
            [$simpleArr, $simpleArrPatternWithUniversalKeyAndIntegerValue],
            [['status' => 'ok', 'data' => [['foo']]], ['status' => 'ok', 'data' => []]],
            [[1], []],
            [['key' => 'val'], ['key' => 'val2']],
            [[1], [2]],
            [['foo', 1, 3], ['foo', 2, 3]],
            [[], ['foo' => 'bar']],
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
}
