<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
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
        $value = [
            'users' => [
                [
                    'id' => 1,
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz',
                    'enabled' => true,
                ],
                [
                    'id' => 2,
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski',
                    'enabled' => true,
                ],
            ],
            'readyToUse' => true,
            'data' => new \stdClass(),
        ];

        $expectation = [
            'users' => [
                [
                    'id' => '@integer@',
                    'firstName' => '@string@',
                    'lastName' => 'Orzechowicz',
                    'enabled' => '@boolean@',
                ],
                [
                    'id' => '@integer@',
                    'firstName' => '@string@',
                    'lastName' => 'Dąbrowski',
                    'enabled' => '@boolean@',
                ],
            ],
            'readyToUse' => true,
            'data' => '@wildcard@',
        ];

        $this->assertTrue($this->matcher->match($value, $expectation), $this->matcher->getError());
        $this->assertTrue(PHPMatcher::match($value, $expectation, $error), (string) $error);
    }

    /**
     * @dataProvider scalarValueExamples
     */
    public function test_matcher_with_scalar_values($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
        $this->assertTrue(PHPMatcher::match($value, $pattern));
    }

    /**
     * @dataProvider jsonDataProvider
     */
    public function test_matcher_with_json($json, $jsonPattern)
    {
        $this->assertTrue($this->matcher->match($json, $jsonPattern));
        $this->assertTrue(PHPMatcher::match($json, $jsonPattern));
    }

    public function jsonDataProvider()
    {
        return [
            'matches exactly' => [
                /** @lang JSON */
                '{
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
                }',
                /** @lang JSON */
                '{
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
                }',
            ],
            'matches none elements - empty array' => [
                /** @lang JSON */
                '{
                    "users":[],
                    "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
                    "nextPage": "http:\/\/example.com\/api\/users\/3?limit=2"
                }',
                /** @lang JSON */
                '{
                    "users":[
                        "@...@"                        
                    ],
                    "prevPage": "@string@",
                    "nextPage": "@string@"
                }',
            ],
            'matches one element' => [
                /** @lang JSON */
                '{
                    "users":[
                        {
                            "id": 131,
                            "firstName": "Norbert",
                            "lastName": "Orzechowicz",
                            "enabled": true,
                            "roles": ["ROLE_DEVELOPER"]
                        }                       
                    ],
                    "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
                    "nextPage": "http:\/\/example.com\/api\/users\/3?limit=2"
                }',
                /** @lang JSON */
                '{
                    "users":[
                        {
                            "id": "@integer@",
                            "firstName":"Norbert",
                            "lastName":"Orzechowicz",
                            "enabled": "@boolean@",
                            "roles": "@array@"
                        },
                        "@...@"
                    ],
                    "prevPage": "@string@",
                    "nextPage": "@string@"
                }',
            ],
            'excludes missing property from match for optional property' => [
                /** @lang JSON */
                '{
                    "users":[],
                    "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
                    "currPage": 2
                }',
                /** @lang JSON */
                '{
                    "users":[
                        "@...@"                        
                    ],
                    "prevPage": "@string@.optional()",
                    "nextPage": "@string@.optional()",
                    "currPage": "@integer@.optional()"
                }',
            ],
        ];
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

    public function test_matcher_with_xml_including_optional_node()
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
    <m:StockName>@string@.optional()</m:StockName>
    <m:StockValue>@string@.optional()</m:StockValue>
    <m:StockQty>@integer@.optional()</m:StockQty>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML;

        $this->assertTrue($this->matcher->match($xml, $xmlPattern));
        $this->assertTrue(PHPMatcher::match($xml, $xmlPattern));
    }

    public function test_text_matcher()
    {
        $value = 'lorem ipsum 1234 random text';
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
        $this->assertTrue($this->matcher->match('test', function ($value) {
            return $value === 'test';
        }));
        $this->assertTrue(PHPMatcher::match('test', function ($value) {
            return $value === 'test';
        }));
        $this->assertFalse($this->matcher->match('test', function ($value) {
            return $value !== 'test';
        }));
        $this->assertFalse(PHPMatcher::match('test', function ($value) {
            return $value !== 'test';
        }));
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
        return [
            ['Norbert Orzechowicz', '@string@'],
            [6.66, '@double@'],
            [1, '@integer@'],
            [['foo'], '@array@'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae18', '@uuid@'],
        ];
    }

    public static function expanderExamples()
    {
        return [
            ['lorem ipsum', '@string@.startsWith("lorem")', true],
            ['lorem ipsum', '@string@.startsWith("LOREM", true)', true],
            ['lorem ipsum', '@string@.endsWith("ipsum")', true],
            ['lorem ipsum', '@string@.endsWith("IPSUM", true)', true],
            ['lorem ipsum', '@string@.contains("lorem")', true],
            ['norbert@coduo.pl', '@string@.isEmail()', true],
            ['lorem ipsum', '@string@.isEmail()', false],
            ['http://coduo.pl/', '@string@.isUrl()', true],
            ['lorem ipsum', '@string@.isUrl()', false],
            ['2014-08-19', '@string@.isDateTime()', true],
            ['3014-08-19', '@string@.before("today")', false],
            ['1014-08-19', '@string@.before("+ 1day")', true],
            ['3014-08-19', '@string@.after("today")', true],
            ['1014-08-19', '@string@.after("+ 1day")', false],
            [100, '@integer@.lowerThan(101).greaterThan(10)', true],
            ['', '@string@.isNotEmpty()', false],
            ['lorem ipsum', '@string@.isNotEmpty()', true],
            ['', '@string@.isEmpty()', true],
            [['foo', 'bar'], '@array@.inArray("bar")', true],
            [[], '@array@.isEmpty()', true],
            [['foo'], '@array@.isEmpty()', false],
            [[1, 2, 3], '@array@.count(3)', true],
            [[1, 2, 3], '@array@.count(4)', false],
            ['lorem ipsum', '@string@.oneOf(contains("lorem"), contains("test"))', true],
            ['lorem ipsum', '@string@.oneOf(contains("lorem"), contains("test")).endsWith("ipsum")', true],
            ['lorem ipsum', '@string@.matchRegex("/^lorem \\w+$/")', true],
            ['lorem ipsum', '@string@.matchRegex("/^foo/")', false],
            [[], ['unexistent_key' => '@array@.optional()'], true],
            [[], ['unexistent_key' => '@boolean@.optional()'], true],
            [[], ['unexistent_key' => '@double@.optional()'], true],
            [[], ['unexistent_key' => '@integer@.optional()'], true],
            [[], ['unexistent_key' => '@json@.optional()'], true],
            [[], ['unexistent_key' => '@number@.optional()'], true],
            [[], ['unexistent_key' => '@scalar@.optional()'], true],
            [[], ['unexistent_key' => '@string@.optional()'], true],
            [[], ['unexistent_key' => '@text@.optional()'], true],
            [[], ['unexistent_key' => '@uuid@.optional()'], true],
            [[], ['unexistent_key' => '@xml@.optional()'], true],
            [['Norbert', 'Michał'], '@array@.repeat("@string@")', true],
            ['127.0.0.1', '@string@.isIp()', true],
            ['2001:0db8:0000:42a1:0000:0000:ab1c:0001', '@string@.isIp()', true],
            ['127.255.999.999', '@string@.isIp()', false],
            ['foo:bar:42:42', '@string@.isIp()', false],
        ];
    }

    public static function orExamples()
    {
        return [
            ['lorem ipsum', '@string@.startsWith("lorem")||@string@.contains("lorem")', true],
            ['ipsum lorem', '@string@.startsWith("lorem")||@string@.contains("lorem")', true],
            ['norbert@coduo.pl', '@string@.isEmail()||@null@', true],
            [null, '@string@.isEmail()||@null@', true],
            [null, '@string@.isEmail()||@null@', true],
            ['2014-08-19', '@string@.isDateTime()||@integer@', true],
            [null, '@integer@||@string@', false],
            [1, '@integer@.greaterThan(10)||@string@.contains("10")', false],
        ];
    }

    /**
     * @dataProvider nullExamples
     */
    public function test_null_value_in_the_json($value, $pattern)
    {
        $factory = new SimpleFactory();
        $matcher = $factory->createMatcher();
        $match = $matcher->match($value, $pattern);
        $this->assertTrue($match, (string) $matcher->getError());
    }

    public static function nullExamples()
    {
        return [
            [
                '{"proformaInvoiceLink":null}', '{"proformaInvoiceLink":null}',
                '{"proformaInvoiceLink":null, "test":"test"}', '{"proformaInvoiceLink":null, "test":"@string@"}',
                '{"proformaInvoiceLink":null, "test":"test"}', '{"proformaInvoiceLink":@null@, "test":"@string@"}',
            ],
        ];
    }

    public static function emptyPatternString()
    {
        return [
            [
                '', '', true,
                '123', '', false,
                ' ', '', false,
                null, '', false,
                1, '', false,
                0, '', false,
                '{"name": "123"}', '{"name": ""}', false,
                '{"name": ""}', '{"name": ""}', true,
            ],
        ];
    }

    /**
     * @dataProvider emptyPatternString
     */
    public function test_empty_pattern_in_the_json($value, $pattern, $expectedResult)
    {
        $factory = new SimpleFactory();
        $matcher = $factory->createMatcher();

        $match = $matcher->match($value, $pattern);
        $this->assertSame($expectedResult, $match);
    }
}
