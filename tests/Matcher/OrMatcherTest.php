<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;
use PHPUnit\Framework\TestCase;

class OrMatcherTest extends TestCase
{
    /**
     * @var Matcher\OrMatcher
     */
    private $matcher;

    public function setUp()
    {
        $factory = new SimpleFactory();
        $this->matcher = $factory->createMatcher();
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match_arrays($value, $pattern)
    {
        $this->assertTrue(
            $this->matcher->match($value, $pattern),
            $this->matcher->getError()
        );
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match_arrays($value, $pattern)
    {
        $this->assertFalse(
            $this->matcher->match($value, $pattern),
            $this->matcher->getError()
        );
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
                    'firstName' => 1,
                    'lastName' => 2
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
                    'lastName' => '@null@||@string@||@integer@'
                ],
                '@...@'
            ],
            true,
            false,
            1,
            6.66
        ];

        return [
            ['test', '@string@'],
            [null, '@array@||@string@||@null@'],
            [
                [
                    'test' => 1
                ],
                [
                    'test' => '@integer@'
                ]
            ],
            [
                [
                    'test' => null
                ],
                [
                    'test' => '@integer@||@null@'
                ]
            ],
            [
                [
                    'first_level' => ['second_level', ['third_level']]
                ],
                '@array@||@null@||@*@'
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
                    'lastName' => '@integer@||@null@||@double@'
                ]
            ],
            true,
            false,
            1,
            6.66
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
}
