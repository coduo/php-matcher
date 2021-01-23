<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace\VoidBacktrace;
use Coduo\PHPMatcher\Factory\MatcherFactory;
use Coduo\PHPMatcher\Matcher;
use PHPUnit\Framework\TestCase;

class OrMatcherTest extends TestCase
{
    private ?\Coduo\PHPMatcher\Matcher $matcher = null;

    public static function positiveMatchData()
    {
        $simpleArr = [
            'users' => [
                [
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz',
                ],
                [
                    'firstName' => 1,
                    'lastName' => 2,
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
                    'lastName' => '@null@||@string@||@integer@',
                ],
                '@...@',
            ],
            true,
            false,
            1,
            6.66,
        ];

        return [
            ['test', '@string@'],
            [null, '@array@||@string@||@null@'],
            [
                [
                    'test' => 1,
                ],
                [
                    'test' => '@integer@',
                ],
            ],
            [
                [
                    'test' => null,
                ],
                [
                    'test' => '@integer@||@null@',
                ],
            ],
            [
                [
                    'first_level' => ['second_level', ['third_level']],
                ],
                '@array@||@null@||@*@',
            ],
            [$simpleArr, $simpleArr],
            [$simpleArr, $simpleArrPattern],
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
                    'lastName' => '@integer@||@null@||@double@',
                ],
            ],
            true,
            false,
            1,
            6.66,
        ];

        return [
            [$simpleArr, $simpleDiff],
            [['status' => 'ok', 'data' => [['foo']]], ['status' => 'ok', 'data' => []]],
            [[1], []],
            [['key' => 'val'], ['key' => 'val2']],
            [[1], [2]],
            [['foo', 1, 3], ['foo', 2, 3]],
            [[], ['foo' => 'bar']],
            [10, '@null@||@integer@.greaterThan(10)'],
        ];
    }

    public function setUp() : void
    {
        $factory = new MatcherFactory();
        $this->matcher = $factory->createMatcher(new VoidBacktrace());
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match_arrays($value, $pattern) : void
    {
        $this->assertTrue(
            $this->matcher->match($value, $pattern),
            (string) $this->matcher->getError()
        );
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match_arrays($value, $pattern) : void
    {
        $this->assertFalse(
            $this->matcher->match($value, $pattern),
            (string) $this->matcher->getError()
        );
    }

    public function test_whitespaces_trim_after_splitting() : void
    {
        $this->assertTrue(
            $this->matcher->match(
                [
                    'test' => null,
                ],
                [
                    'test' => ' @integer@ || @null@ ',
                ]
            ),
            (string) $this->matcher->getError()
        );
    }
}
