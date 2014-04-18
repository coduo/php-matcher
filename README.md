#PHP Matcher

***PHP Matcher*** lets You assert like a gangster in Your test cases, where response can be something you cannot predict

[![Build Status](https://travis-ci.org/defrag/php-matcher.svg)](https://travis-ci.org/defrag/php-matcher)

##Installation

Add to your composer.json 

```
require: {
   "defrag/php-matcher": "dev-master"
}
```

From now you should be able to use global function ``match($value, $pattern)``

##Example usage

### Scalar matching

```php
<?php

match(1, 1);
match('string', 'string')
```

### Type matching

```php
<?php

match(1, '@integer@');
match('Norbert', '@string@');
match(array('foo', 'bar'), '@array');
match(12.4, '@double@');
match(true, '@boolean@');
```

### Wildcard 

```php
<?php

match(1, '@*@');
match(new \stdClass(), '@wildcard@');
```

### Expression matching 

```php
<?php

match(new \DateTime('2014-04-01'), "expr(value.format('Y-m-d') == '2014-04-01'");
match("Norbert", "expr(value === 'Norbert')");
```

### Array matching 

```php
<?php

match(
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
          )
      ),
      true,
      6.66
  ),
   array(
      'users' => array(
          array(
              'id' => '@integer@',
              'firstName' => '@string@',
              'lastName' => 'Orzechowicz',
              'roles' => '@array@'
          ),
          array(
              'id' => '@integer@'
              'firstName' => '@string@',
              'lastName' => 'Dąbrowski',
              'roles' => '@array@'
          )
      ),
      '@boolean@',
      '@double@'
  )  
)
```

### Json matching 


```php
<?php

match(
  '{
    "users":[
      {
        "firstName": "Norbert",
        "lastName": "Orzechowicz",
        "roles":["ROLE_USER", "ROLE_DEVELOPER"]}
      ]
  }',
  '{
    "users":[
      {
        "firstName": "@string@",
        "lastName":" @string@",
        "roles": "@array@"
      }
    ]
  }'
)

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
          "id": "@string@",
          "name": "Barbie",
          "_links: "@*@"
        },
        {
          "id": "@string@",
          "name": "GI Joe",
          "_links": "@*@"
        },
        {
          "id": "@string@",
          "name": "Optimus Prime",
          "_links": "@*@"
        }
      ]
    """
``` 
