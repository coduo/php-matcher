#PHP Matcher

***PHP Matcher*** lets You assert like a gangster in Your test cases, where response can be something you cannot predict

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/coduo/php-matcher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/coduo/php-matcher/?branch=master)

* [![Build Status](https://travis-ci.org/coduo/php-matcher.svg)](https://travis-ci.org/coduo/php-matcher) - master
* [![Build Status](https://travis-ci.org/coduo/php-matcher.svg?branch=1.1)](https://travis-ci.org/coduo/php-matcher) - 1.1.*
* [![Build Status](https://travis-ci.org/coduo/php-matcher.svg?branch=1.0)](https://travis-ci.org/coduo/php-matcher) - 1.0.*

[Readme for master version](https://github.com/coduo/php-matcher/tree/master/README.md)  
[Readme for 1.1 version](https://github.com/coduo/php-matcher/tree/1.1/README.md)  
[Readme for 1.0 version](https://github.com/coduo/php-matcher/tree/1.0/README.md)


##Installation

Require new dev dependency using composer (assuming it's installed globally):

```
composer require --dev "coduo/php-matcher"
```

## Basic usage

### Using facade

```php
<?php

use Coduo\PHPMatcher\PHPMatcher;

if (!PHPMatcher::match("lorem ipsum dolor", "@string@", $error)) { 
    echo $error; // in case of error message is set on $error variable via reference
}

```


### Using Factory

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$match = $matcher->match("lorem ipsum dolor", "@string@");
// $match === true
$matcher->getError(); // returns null or error message
```

### Available patterns

* ``@string@``
* ``@integer@``
* ``@number@``
* ``@double@``
* ``@boolean@``
* ``@array@``
* ``@...@`` - *unbounded array*
* ``@null@``
* ``@*@`` || ``@wildcard@``
* ``expr(expression)``
* ``@uuid@``

### Available pattern expanders

* ``startsWith($stringBeginning, $ignoreCase = false)``
* ``endsWith($stringEnding, $ignoreCase = false)``
* ``contains($string, $ignoreCase = false)``
* ``isDateTime()``
* ``isEmail()``
* ``isUrl()``
* ``isEmpty()``
* ``isNotEmpty()``
* ``lowerThan($boundry)``
* ``greaterThan($boundry)``
* ``inArray($value)``
* ``oneOf(...$expanders)`` - example usage ``"@string@.oneOf(contains('foo'), contains('bar'), contains('baz'))"``
* ``matchRegex($regex)`` - example usage ``"@string@.matchRegex('/^lorem.+/')"``

##Example usage

### Scalar matching

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match(1, 1);
$matcher->match('string', 'string')
```

### String matching

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match('Norbert', '@string@');
$matcher->match("lorem ipsum dolor", "@string@.startsWith('lorem').contains('ipsum').endsWith('dolor')")

```

### Integer matching

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match(100, '@integer@');
$matcher->match(100, '@integer@.lowerThan(200).greaterThan(10)');

```

### Number matching

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match(100, '@number@');
$matcher->match('200', '@number@');
$matcher->match(1.25, '@number@');
$matcher->match('1.25', '@number@');
$matcher->match(0b10100111001, '@number@');
```

### Double matching

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match(10.1, "@double@");
$matcher->match(10.1, "@double@.lowerThan(50.12).greaterThan(10)");
```

### Boolean matching

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match(true, "@boolean@");
$matcher->match(false, "@boolean@");
```

### Wildcard matching

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match("@integer@", "@*@"),
$matcher->match("foobar", "@*@"),
$matcher->match(true, "@*@"),
$matcher->match(6.66, "@*@"),
$matcher->match(array("bar"), "@wildcard@"),
$matcher->match(new \stdClass, "@wildcard@"),
```

### Expression matching 

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match(new \DateTime('2014-04-01'), "expr(value.format('Y-m-d') == '2014-04-01'");
$matcher->match("Norbert", "expr(value === 'Norbert')");
```

### UUID matching

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match('9f4db639-0e87-4367-9beb-d64e3f42ae18', '@uuid@');
```

### Array matching 

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match(
   array(
      'users' => array(
          array(
              'id' => 1,
              'firstName' => 'Norbert',
              'lastName' => 'Orzechowicz',
              'roles' => array('ROLE_USER')
          ),
          array(
              'id' => 2,
              'firstName' => 'Michał',
              'lastName' => 'Dąbrowski',
              'roles' => array('ROLE_USER')
          ),
          array(
              'id' => 3,
              'firstName' => 'Johnny',
              'lastName' => 'DąbrowsBravoki',
              'roles' => array('ROLE_HANDSOME_GUY')
          )
      ),
      true,
      6.66
  ),
   array(
      'users' => array(
          array(
              'id' => '@integer@.greaterThan(0)',
              'firstName' => '@string@',
              'lastName' => 'Orzechowicz',
              'roles' => '@array@'
          ),
          array(
              'id' => '@integer@',
              'firstName' => '@string@',
              'lastName' => 'Dąbrowski',
              'roles' => '@array@'
          ),
          '@...@'
      ),
      '@boolean@',
      '@double@'
  )  
)
```

### Json matching 

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match(
  '{
    "users":[
      {
        "firstName": "Norbert",
        "lastName": "Orzechowicz",
        "created": "2014-01-01",
        "roles":["ROLE_USER", "ROLE_DEVELOPER"]}
      ]
  }',
  '{
    "users":[
      {
        "firstName": @string@,
        "lastName": @string@,
        "created": "@string@.isDateTime()",
        "roles": @array@
      }
    ]
  }'
)

```

### Xml matching

```php
<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

$factory = new SimpleFactory();
$matcher = $factory->createMatcher();

$matcher->match(<<<XML
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
XML
                ,
                <<<XML
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
XML
        );
```

Example scenario for api in behat using mongo.
---
``` cucumber
@profile, @user
Feature: Listing user toys

  As a user
  I want to list my toys

  Background:
    Given I send and accept JSON

  Scenario: Listing toys
    Given the following users exist:
      | firstName     | lastName     |
      | Chuck         | Norris       | 

    And the following toys user "Chuck Norris" exist:
      | name            |
      | Barbie          |
      | GI Joe          |
      | Optimus Prime   |

    When I set valid authorization code oauth header for user "Chuck Norris"
    And I send a GET request on "/api/toys"
    Then the response status code should be 200
    And the JSON response should match:
    """
      [
        {
          "id": @string@,
          "name": "Barbie",
          "_links: "@*@"
        },
        {
          "id": @string@,
          "name": "GI Joe",
          "_links": "@*@"
        },
        {
          "id": @string@,
          "name": "Optimus Prime",
          "_links": "@*@"
        }
      ]
    """
``` 

## PHPUnit integration

The `assertMatchesPattern()` is a handy assertion that matches values in PHPUnit tests.
To use it either include the `Coduo\PHPMatcher\PHPUnit\PHPMatcherAssertions` trait,
or extend the `Coduo\PHPMatcher\PHPUnit\PHPMatcherTestCase`:

```php
namespace Coduo\PHPMatcher\Tests\PHPUnit;

use Coduo\PHPMatcher\PHPUnit\PHPMatcherAssertions;

class PHPMatcherAssertionsTest extends \PHPUnit_Framework_TestCase
{
    use PHPMatcherAssertions;

    public function test_it_asserts_if_a_value_matches_the_pattern()
    {
        $this->assertMatchesPattern('@string@', 'foo');
    }
}
```

The `matchesPattern()` method can be used in PHPUnit stubs or mocks:

```php
$mock = $this->getMock(Foo::class);
$mock->method('bar')
    ->with($this->matchesPattern('@string@'))
    ->willReturn('foo');
```

## License

This library is distributed under the MIT license. Please see the LICENSE file.

## Credits

This lib was inspired by [JSON Expressions gem](https://github.com/chancancode/json_expressions) &&
[Behat RestExtension ](https://github.com/jakzal/RestExtension)

