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

//    /**
//     * @dataProvider negativeMatchData
//     */
//    public function test_negative_match_arrays($value, $pattern)
//    {
//        $this->assertFalse($this->matcher->match($value, $pattern));
//    }
//
//    public function test_negative_match_when_cant_find_matcher_that_can_match_array_element()
//    {
//        $matcher = new Matcher\ArrayMatcher(
//            new Matcher\ChainMatcher(array(
//                new Matcher\WildcardMatcher()
//            )),
//            $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer())
//        );
//
//        $this->assertTrue($matcher->match(array('test' => 1), array('test' => 1)));
//    }
//
//    public function test_error_when_path_in_pattern_does_not_exist()
//    {
//        $this->assertFalse($this->matcher->match(array('foo' => 'foo value'), array('bar' => 'bar value')));
//        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo] in pattern.');
//    }
//
//    public function test_error_when_path_in_nested_pattern_does_not_exist()
//    {
//        $array = array('foo' => array('bar' => array('baz' => 'bar value')));
//        $pattern = array('foo' => array('bar' => array('faz' => 'faz value')));
//
//        $this->assertFalse($this->matcher->match($array, $pattern));
//
//        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][baz] in pattern.');
//    }
//
//    public function test_error_when_path_in_value_does_not_exist()
//    {
//        $array = array('foo' => 'foo');
//        $pattern = array('foo' => 'foo', 'bar' => 'bar');
//
//        $this->assertFalse($this->matcher->match($array, $pattern));
//
//        $this->assertEquals($this->matcher->getError(), 'There is no element under path [bar] in value.');
//    }
//
//    public function test_error_when_path_in_nested_value_does_not_exist()
//    {
//        $array = array('foo' => array('bar' => array()));
//        $pattern = array('foo' => array('bar' => array('faz' => 'faz value')));
//
//        $this->assertFalse($this->matcher->match($array, $pattern));
//
//        $this->assertEquals($this->matcher->getError(), 'There is no element under path [foo][bar][faz] in value.');
//    }
//
//    public function test_error_when_matching_fail()
//    {
//        $this->assertFalse($this->matcher->match(array('foo' => 'foo value'), array('foo' => 'bar value')));
//        $this->assertEquals($this->matcher->getError(), '"foo value" does not match "bar value".');
//    }
//
//    public function test_error_message_when_matching_non_array_value()
//    {
//        $this->assertFalse($this->matcher->match(new \DateTime(), "@array@"));
//        $this->assertEquals($this->matcher->getError(), "object "\\DateTime" is not a valid array.");
//    }
//
//    public function test_matching_array_to_array_pattern()
//    {
//        $this->assertTrue($this->matcher->match(array("foo", "bar"), "@array@"));
//        $this->assertTrue($this->matcher->match(array("foo"), "@array@.inArray("foo")"));
//        $this->assertTrue($this->matcher->match(
//            array("foo", array("bar")),
//            array(
//                "@string@",
//                "@array@.inArray("bar")"
//            )
//        ));
//    }

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
//            [
//                [[
//                    'firstName' => 'Norbert',
//                    'lastName' => 'Orzechowicz'
//                ]],
//                '@json@.match({
//                        "firstName": "@string@",
//                        "lastName": "@string@"
//                    })
//                '
//            ],
//            [
//                [
//                    "id" => 1,
//                    "groups" => [
//                        ["name" => 'asdas'],
//                        ["name" => 1],
//                    ]
//                ],
//                [
//                    "id" => "@integer@",
//                    "groups" => '
//                        @json@.match({
//                            "name": "@string@||@integer@"
//                        })
//                    '
//                ],
//            ],
//            [
//                [
//                    "id" => 1,
//                    "gallery" => [
//                        "id" => 1,
//                        "images" => [['id' => 1], ['id' => 2]],
//                    ]
//                ],
//                [
//                    "id" => "@integer@",
//                    "gallery" => [
//                        'id' => '@integer@',
//                        'images' => '
//                            @json@.match({
//                                "id": "@integer@"
//                            })
//                        '
//                    ]
//                ],
//            ],
//            [
//                [
//                    "id" => 1,
//                    "galleries" => [[
//                        "id" => 1,
//                        "cover" => null,
//                        "images" => [['id' => 1], ['id' => 2]],
//                    ]]
//                ],
//                [
//                    "id" => "@integer@",
//                    "galleries" => '
//                        @json@.match({
//                            "id": "@integer@",
//                            "cover": \'
//                                @null@||@json@.match({
//                                    "id": "@integer@"
//                                })
//                            \',
//                            "images": \'
//                                @json@.match({
//                                    "id": "@integer@"
//                                })
//                            \'
//                        })
//                    '
//                ],
//            ]
        );
    }

    public static function negativeMatchData()
    {
        $simpleArr = array(
            'users' => array(
                array(
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ),
                array(
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski'
                )
            ),
            true,
            false,
            1,
            6.66
        );

        $simpleDiff = array(
            'users' => array(
                array(
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ),
                array(
                    'firstName' => 'Pablo',
                    'lastName' => 'Dąbrowski'
                )
            ),
            true,
            false,
            1,
            6.66
        );

        return array(
            array($simpleArr, $simpleDiff),
            array(array("status" => "ok", "data" => array(array('foo'))), array("status" => "ok", "data" => array())),
            array(array(1), array()),
            array(array('key' => 'val'), array('key' => 'val2')),
            array(array(1), array(2)),
            array(array('foo', 1, 3), array('foo', 2, 3)),
            array(array(), array('foo' => 'bar')),
            'unbound array should match one or none elements' => array(
                array(
                    'users' => array(
                        array(
                            'firstName' => 'Norbert',
                            'lastName' => 'Foobar',
                        ),
                    ),
                    true,
                    false,
                    1,
                    6.66,
                ),
                $simpleDiff,
            ),
        );
    }
}
