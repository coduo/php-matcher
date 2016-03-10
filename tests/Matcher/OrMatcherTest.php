<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;

class OrMatcherTest extends \PHPUnit_Framework_TestCase
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
        $simpleArr = array(
            'users' => array(
                array(
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz'
                ),
                array(
                    'firstName' => 1,
                    'lastName' => 2
                )
            ),
            true,
            false,
            1,
            6.66
        );

        $simpleArrPattern = array(
            'users' => array(
                array(
                    'firstName' => '@string@',
                    'lastName' => '@null@||@string@||@integer@'
                ),
                '@...@'
            ),
            true,
            false,
            1,
            6.66
        );

        return array(
            array('test', '@string@'),
            array(null, '@array@||@string@||@null@'),
            array(
                array(
                    'test' => 1
                ),
                array(
                    'test' => '@integer@'
                )
            ),
            array(
                array(
                    'test' => null
                ),
                array(
                    'test' => '@integer@||@null@'
                )
            ),
            array(
                array(
                    'first_level' => array('second_level', array('third_level'))
                ),
                '@array@||@null@||@*@'
            ),
            array($simpleArr, $simpleArr),
            array($simpleArr, $simpleArrPattern),
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
                    'lastName' => '@integer@||@null@||@double@'
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
            array(10, '@null@||@integer@.greaterThan(10)'),
        );
    }
}
