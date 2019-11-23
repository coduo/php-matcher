<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\PHPUnit\PHPMatcherTestCase;

class MatcherTest extends PHPMatcherTestCase
{
    /**
     * @dataProvider scalarValueExamples
     */
    public function test_matcher_with_scalar_values($value, $pattern)
    {
        $this->assertMatchesPattern($pattern, $value);
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

        $pattern = [
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

        $this->assertMatchesPattern($pattern, $value);
    }

    /**
     * @dataProvider jsonDataProvider
     */
    public function test_matcher_with_json($value, $pattern)
    {
        $this->assertMatchesPattern($pattern, $value);
    }

    public function test_matcher_with_xml()
    {
        $value = <<<XML
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
        $pattern = <<<XML
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

        $this->assertMatchesPattern($pattern, $value);
    }

    public function test_matcher_with_xml_including_optional_node()
    {
        $value = <<<XML
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
        $pattern = <<<XML
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

        $this->assertMatchesPattern($pattern, $value);
    }

    public function test_full_text_matcher()
    {
        $value = 'lorem ipsum 1234 random text';
        $pattern = "@string@.startsWith('lo') ipsum @number@.greaterThan(10) random text";
        $this->assertMatchesPattern($pattern, $value);
    }

    public function test_matcher_with_callback()
    {
        $this->assertMatchesPattern(
            function ($value) {
                return $value === 'test';
            },
            'test'
        );
    }

    public function test_matcher_with_wildcard()
    {
        $this->assertMatchesPattern('@*@', 'test');
        $this->assertMatchesPattern('@wildcard@', 'test');
    }

    /**
     * @dataProvider nullExamples
     */
    public function test_null_value_in_the_json(string $value, string $pattern)
    {
        $this->assertMatchesPattern($pattern, $value);
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

    public function jsonDataProvider()
    {
        return [
            [
                '{"data": {"createUserFormSchema":{"formData":"test","schema":"test","uiSchema":"test"}}}',
                '{"data": {"createUserFormSchema":"@json@.hasProperty(\"formData\").hasProperty(\"schema\").hasProperty(\"uiSchema\")"}}'
            ],
            [
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
                '@json@.hasProperty("users")'
            ],
            [
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
            [
                /** @lang JSON */
                '{
                    "url": "/accounts/9a7dae2d-d135-4bd7-b202-b3e7e91aaecd"
                }',
                /** @lang JSON */
                '{
                    "url": "/accounts/@uuid@"
                }',
            ],
            [
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
            [
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
            [
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
            [
                /** @lang JSON */
                '{
                    "user": {
                        "id": 131,
                        "firstName": "Norbert",
                        "lastName": "Orzechowicz",
                        "enabled": true,
                        "roles": ["ROLE_DEVELOPER"]
                    }
                }',
                /** @lang JSON */
                '{
                    "user": "@json@"
                }',
            ],
            [
                /** @lang JSON */
                '{
                    "user": null
                }',
                /** @lang JSON */
                '{
                    "user": "@json@.optional()"
                }',
            ],
        ];
    }
}
