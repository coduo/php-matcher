<?php 
namespace JsonMatcher\Tests;

use JsonMatcher\ArrayMatcher;

class ArrayMatcherTest extends \PHPUnit_Framework_TestCase
{
    function test_matcher()
    {
        $matcher = new ArrayMatcher([
            'users' => [
                [
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ],
                [
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski'
                ]
            ]
        ]);

        $this->assertTrue($matcher->match([
            'users' => [
                [
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ],
                [
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski'
                ]
            ]
        ]));
    }
}
