<?php

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;

class MatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Matcher
     */
    protected $matcher;

    protected $arrayValue;

    public function setUp()
    {
        $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer());
        $scalarMatchers = new Matcher\ChainMatcher(array(
            new Matcher\CallbackMatcher(),
            new Matcher\ExpressionMatcher(),
            new Matcher\NullMatcher(),
            new Matcher\StringMatcher($parser),
            new Matcher\IntegerMatcher($parser),
            new Matcher\BooleanMatcher(),
            new Matcher\DoubleMatcher($parser),
            new Matcher\NumberMatcher(),
            new Matcher\ScalarMatcher(),
            new Matcher\WildcardMatcher(),
        ));

        $arrayMatcher = new Matcher\ArrayMatcher($scalarMatchers, $parser);

        $this->matcher = new Matcher(new Matcher\ChainMatcher(array(
            $scalarMatchers,
            $arrayMatcher,
            new Matcher\JsonMatcher($arrayMatcher),
            new Matcher\XmlMatcher($arrayMatcher),
            new Matcher\TextMatcher($scalarMatchers, $parser)
        )));
    }

    public function test_matcher_with_array_value()
    {
        $value = array(
            'users' => array(
                array(
                    'id' => 1,
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz',
                    'enabled' => true
                ),
                array(
                    'id' => 2,
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski',
                    'enabled' => true,
                )
            ),
            'readyToUse' => true,
            'data' => new \stdClass(),
        );

        $expecation = array(
            'users' => array(
                array(
                    'id' => '@integer@',
                    'firstName' => '@string@',
                    'lastName' => 'Orzechowicz',
                    'enabled' => '@boolean@'
                ),
                array(
                    'id' => '@integer@',
                    'firstName' => '@string@',
                    'lastName' => 'Dąbrowski',
                    'enabled' => '@boolean@',
                )
            ),
            'readyToUse' => true,
            'data' => '@wildcard@',
        );

        $this->assertTrue($this->matcher->match($value, $expecation), $this->matcher->getError());
    }

    /**
     * @dataProvider scalarValues
     */
    public function test_matcher_with_scalar_values($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
    }

    public function scalarValues()
    {
        return array(
            array('Norbert Orzechowicz', '@string@'),
            array(6.66, '@double@'),
            array(1, '@integer@'),
            array(array('foo'), '@array@')
        );
    }

    public function test_matcher_with_json()
    {
        $json = '
        {
            "users":[
                {
                    "id": 131,
                    "firstName": "Norbert",
                    "lastName": "Orzechowicz",
                    "enabled": true,
                    "roles": ["ROLE_DEVELOPER"]
                },
                {
                    "id": 132,
                    "firstName": "Michał",
                    "lastName": "Dąbrowski",
                    "enabled": false,
                    "roles": ["ROLE_DEVELOPER"]
                }
            ],
            "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
            "nextPage": "http:\/\/example.com\/api\/users\/3?limit=2"
        }';
        $jsonPattern = '
        {
            "users":[
                {
                    "id": "@integer@",
                    "firstName":"Norbert",
                    "lastName":"Orzechowicz",
                    "enabled": "@boolean@",
                    "roles": "@array@"
                },
                {
                    "id": "@integer@",
                    "firstName": "Michał",
                    "lastName": "Dąbrowski",
                    "enabled": "expr(value == false)",
                    "roles": "@array@"
                }
            ],
            "prevPage": "@string@",
            "nextPage": "@string@"
        }';

        $this->assertTrue($this->matcher->match($json, $jsonPattern));
    }

    public function test_matcher_with_xml()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<soap:Envelope
xmlns:soap="http://www.w3.org/2001/12/soap-envelope"
soap:encodingStyle="http://www.w3.org/2001/12/soap-encoding">

<soap:Body xmlns:m="http://www.example.org/stock">
  <m:GetStockPrice>
    <m:StockName>IBM</m:StockName>
    <m:StockValue>Any Value</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML;
        $xmlPattern = <<<XML
<?xml version="1.0"?>
<soap:Envelope
    xmlns:soap="@string@"
            soap:encodingStyle="@string@">

<soap:Body xmlns:m="@string@">
  <m:GetStockPrice>
    <m:StockName>@string@</m:StockName>
    <m:StockValue>@string@</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML;

        $this->assertTrue($this->matcher->match($xml, $xmlPattern));
    }

    public function test_text_matcher()
    {
        $value = "lorem ipsum 1234 random text";
        $pattern = "@string@.startsWith('lo') ipsum @number@.greaterThan(10) random text";
        $this->assertTrue($this->matcher->match($value, $pattern));
    }


    public function test_error_when_json_value_does_not_match_json_pattern()
    {
        $pattern = '{"a": @null@, "b": 4}';
        $value = '{"a": null, "b": 5}';

        $this->assertFalse($this->matcher->match($value, $pattern));
        $this->assertSame('"5" does not match "4".', $this->matcher->getError());
    }

    public function test_matcher_with_callback()
    {
        $this->assertTrue($this->matcher->match('test', function($value) { return $value === 'test';}));
        $this->assertFalse($this->matcher->match('test', function($value) { return $value !== 'test';}));
    }

    public function test_matcher_with_wildcard()
    {
        $this->assertTrue($this->matcher->match('test', '@*@'));
        $this->assertTrue($this->matcher->match('test', '@wildcard@'));
    }

    /**
     * @dataProvider expanderExamples()
     */
    public function test_expanders($value, $pattern, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->matcher->match($value, $pattern));
    }

    public static function expanderExamples()
    {
        return array(
            array("lorem ipsum", "@string@.startsWith(\"lorem\")", true),
            array("lorem ipsum", "@string@.startsWith(\"LOREM\", true)", true),
            array("lorem ipsum", "@string@.endsWith(\"ipsum\")", true),
            array("lorem ipsum", "@string@.endsWith(\"IPSUM\", true)", true),
            array("lorem ipsum", "@string@.contains(\"lorem\")", true),
            array("norbert@coduo.pl", "@string@.isEmail()", true),
            array("lorem ipsum", "@string@.isEmail()", false),
            array("http://coduo.pl/", "@string@.isUrl()", true),
            array("lorem ipsum", "@string@.isUrl()", false),
            array("2014-08-19", "@string@.isDateTime()", true),
            array(100, "@integer@.lowerThan(101).greaterThan(10)", true),
            array("", "@string@.notEmpty()", false),
            array("lorem ipsum", "@string@.notEmpty()", true),
            array("", "@string@.isEmpty()", true),
            array(array("foo", "bar"), "@array@.inArray(\"bar\")", true),
            array(array(), "@array@.isEmpty()", true),
            array(array('foo'), "@array@.isEmpty()", false),
            array("lorem ipsum", "@string@.oneOf(contains(\"lorem\"), contains(\"test\"))", true),
            array("lorem ipsum", "@string@.oneOf(contains(\"lorem\"), contains(\"test\")).endsWith(\"ipsum\")", true),
            array("lorem ipsum", "@string@.matchRegex(\"/^lorem \\w+$/\")", true),
            array("lorem ipsum", "@string@.matchRegex(\"/^foo/\")", false),
        );
    }
}
