<?php

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;
use Coduo\PHPMatcher\PHPMatcher;

class MatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Matcher
     */
    protected $matcher;

    public function setUp()
    {
        $factory = new SimpleFactory();
        $this->matcher = $factory->createMatcher();
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

        $expectation = array(
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

        $this->assertTrue($this->matcher->match($value, $expectation), $this->matcher->getError());
        $this->assertTrue(PHPMatcher::match($value, $expectation, $error), $error);
    }

    /**
     * @dataProvider scalarValueExamples
     */
    public function test_matcher_with_scalar_values($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
        $this->assertTrue(PHPMatcher::match($value, $pattern));
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
        $this->assertTrue(PHPMatcher::match($json, $jsonPattern));
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
        $this->assertTrue(PHPMatcher::match($xml, $xmlPattern));
    }

    public function test_text_matcher()
    {
        $value = "lorem ipsum 1234 random text";
        $pattern = "@string@.startsWith('lo') ipsum @number@.greaterThan(10) random text";
        $this->assertTrue($this->matcher->match($value, $pattern));
        $this->assertTrue(PHPMatcher::match($value, $pattern));
    }

    public function test_error_when_json_value_does_not_match_json_pattern()
    {
        $pattern = '{"a": @null@, "b": 4}';
        $value = '{"a": null, "b": 5}';

        $this->assertFalse($this->matcher->match($value, $pattern));
        $this->assertSame('"5" does not match "4".', $this->matcher->getError());

        $this->assertFalse(PHPMatcher::match($value, $pattern, $error));
        $this->assertSame('"5" does not match "4".', $error);
    }

    public function test_matcher_with_callback()
    {
        $this->assertTrue($this->matcher->match('test', function($value) { return $value === 'test';}));
        $this->assertTrue(PHPMatcher::match('test', function($value) { return $value === 'test';}));
        $this->assertFalse($this->matcher->match('test', function($value) { return $value !== 'test';}));
        $this->assertFalse(PHPMatcher::match('test', function($value) { return $value !== 'test';}));
    }

    public function test_matcher_with_wildcard()
    {
        $this->assertTrue($this->matcher->match('test', '@*@'));
        $this->assertTrue(PHPMatcher::match('test', '@*@'));
        $this->assertTrue($this->matcher->match('test', '@wildcard@'));
        $this->assertTrue(PHPMatcher::match('test', '@wildcard@'));
    }

    /**
     * @dataProvider orExamples()
     */
    public function test_matcher_with_or($value, $pattern, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->matcher->match($value, $pattern));
        $this->assertSame($expectedResult, PHPMatcher::match($value, $pattern));
    }

    /**
     * @dataProvider expanderExamples()
     */
    public function test_expanders($value, $pattern, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->matcher->match($value, $pattern));
        $this->assertSame($expectedResult, PHPMatcher::match($value, $pattern));
    }

    public function scalarValueExamples()
    {
        return array(
            array('Norbert Orzechowicz', '@string@'),
            array(6.66, '@double@'),
            array(1, '@integer@'),
            array(array('foo'), '@array@'),
            array('9f4db639-0e87-4367-9beb-d64e3f42ae18', '@uuid@'),
        );
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
            array("", "@string@.isNotEmpty()", false),
            array("lorem ipsum", "@string@.isNotEmpty()", true),
            array("", "@string@.isEmpty()", true),
            array(array("foo", "bar"), "@array@.inArray(\"bar\")", true),
            array(array(), "@array@.isEmpty()", true),
            array(array('foo'), "@array@.isEmpty()", false),
            array(array(1, 2, 3), "@array@.count(3)", true),
            array(array(1, 2, 3), "@array@.count(4)", false),
            array("lorem ipsum", "@string@.oneOf(contains(\"lorem\"), contains(\"test\"))", true),
            array("lorem ipsum", "@string@.oneOf(contains(\"lorem\"), contains(\"test\")).endsWith(\"ipsum\")", true),
            array("lorem ipsum", "@string@.matchRegex(\"/^lorem \\w+$/\")", true),
            array("lorem ipsum", "@string@.matchRegex(\"/^foo/\")", false),
        );
    }

    public static function orExamples()
    {
        return array(
            array("lorem ipsum", "@string@.startsWith(\"lorem\")||@string@.contains(\"lorem\")", true),
            array("norbert@coduo.pl", "@string@.isEmail()||@null@", true),
            array(null, "@string@.isEmail()||@null@", true),
            array(null, "@string@.isEmail()||@null@", true),
            array("2014-08-19", "@string@.isDateTime()||@integer@", true),
            array(null, "@integer@||@string@", false),
            array(1, "@integer@.greaterThan(10)||@string@.contains(\"10\")", false),
        );
    }

    /**
     * @dataProvider nullExamples
     */
    public function test_null_value_in_the_json($value, $pattern)
    {
        $factory = new SimpleFactory();
        $matcher = $factory->createMatcher();
        $match = $matcher->match($value, $pattern);
        $this->assertTrue($match, $matcher->getError());
    }

    public static function nullExamples()
    {
        return array(
            array(
                '{"proformaInvoiceLink":null}', '{"proformaInvoiceLink":null}',
                '{"proformaInvoiceLink":null, "test":"test"}', '{"proformaInvoiceLink":null, "test":"@string@"}',
                '{"proformaInvoiceLink":null, "test":"test"}', '{"proformaInvoiceLink":@null@, "test":"@string@"}',
            )
        );
    }
}
