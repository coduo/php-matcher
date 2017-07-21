<?php

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;

class JsonObjectMatcherTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Matcher\ArrayMatcher
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
        $this->assertTrue($this->matcher->match($value, $pattern), $this->matcher->getError());
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match_arrays($value, $pattern)
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
    }

    public static function positiveMatchData()
    {
        return array(
            [
                [
                    [
                        'firstName' => 'Norbert',
                        'lastName' => 'Orzechowicz'
                    ]
                ],
                '@null@||@json@.match({
                        "firstName": "@string@",
                        "lastName": "@string@"
                    })
                '
            ],
            [
                [[
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ]],
                '@json@.match({
                        "firstName": "@string@",
                        "lastName": "@string@"
                    })
                '
            ],
            [
                [
                    "id" => 1,
                    "groups" => [
                        ["name" => 'asdas'],
                        ["name" => 1],
                    ]
                ],
                [
                    "id" => "@integer@",
                    "groups" => '
                        @json@.match({
                            "name": "@string@||@integer@"
                        })
                    '
                ],
            ],
            [
                [
                    "id" => 1,
                    "gallery" => [
                        "id" => 1,
                        "images" => [['id' => 1], ['id' => 2]],
                    ]
                ],
                [
                    "id" => "@integer@",
                    "gallery" => [
                        'id' => '@integer@',
                        'images' => '
                            @json@.match({
                                "id": "@integer@"
                            })
                        '
                    ]
                ],
            ],
            [
                [
                    "id" => 1,
                    "galleries" => [[
                        "id" => 1,
                        "cover" => null,
                        "images" => [['id' => 1], ['id' => 2]],
                    ]]
                ],
                [
                    "id" => "@integer@",
                    "galleries" => '
                        @json@.match({
                            "id": "@integer@",
                            "cover": \'
                                @null@||@json@.match({
                                    "id": "@integer@"
                                })
                            \',
                            "images": \'
                                @json@.match({
                                    "id": "@integer@"
                                })
                            \'
                        })
                    '
                ],
            ],
            [
                ["photos" => [1, 2, 2, null]],
                ["photos" => '@json@.match("@null@||@integer@")']
            ]
        );
    }

    public static function negativeMatchData()
    {
        return array(
            [
                [
                    [
                        'firstName' => 'Norbert',
                        'lastName' => 'Orzechowicz'
                    ]
                ],
                '@null@||@json@.match({
                        "avatar": "@null@",
                        "firstName": "@string@",
                        "lastName": "@string@"
                    })
                '
            ],
            [
                [[
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ]],
                '@json@.match({
                        "firstName": "@string@",
                        "lastName": "@string@",
                        "email": "@string@.isEmail()"
                    })
                '
            ],
            [
                [
                    "id" => 1,
                    "groups" => [
                        ["name" => 'asdas'],
                        ["name" => 1],
                    ]
                ],
                [
                    "id" => "@integer@",
                    "groups" => '
                        @json@.match("@array@.count(0)")
                    '
                ],
            ],
            [
                [
                    "id" => 1,
                    "gallery" => [
                        "id" => 1,
                        "images" => [['id' => 1], ['id' => 2]],
                    ]
                ],
                [
                    "id" => "@integer@",
                    "gallery" => [
                        'id' => '@integer@',
                        'images' => '
                            @json@.match({
                                "id": "@integer@",
                                "url": "@string@.isUrl()"
                            })
                        '
                    ]
                ],
            ],
            [
                [
                    "id" => 1,
                    "galleries" => [[
                        "id" => 1,
                        "cover" => null,
                        "images" => [['id' => 1], ['id' => 2]],
                    ]]
                ],
                [
                    "id" => "@integer@",
                    "galleries" => '
                        @json@.match({
                            "id": "@integer@",
                            "cover": \'
                                @json@.match({
                                    "id": "@integer@"
                                })
                            \',
                            "images": \'
                                @json@.match({
                                    "id": "@integer@"
                                })
                            \'
                        })
                    '
                ],
            ],
            [
                ["photos" => [1, 2, 2.0, null]],
                ["photos" => '@json@.match("@null@||@double@")']
            ]
        );
    }
}
