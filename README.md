JsonMatcher
========
***JsonMatcher*** lets You assert Your json like a gangster in Your test cases.

[![Build Status](https://travis-ci.org/defrag/JsonMatcher.svg)](https://travis-ci.org/defrag/JsonMatcher)

Example scenario for api in behat using mongo.
---
``` cucumber
@profile, @user
Feature: Listing user toys

  As a user
  I want to list my toys

  Background:
    Given I send and accept JSON

  Scenario: Listing timesheets
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
          "_links: "*"
        },
        {
          "id": "@string@",
          "name": "GI Joe",
          "_links": "*"
        },
        {
          "id": "@string@",
          "name": "Optimus Prime",
          "_links": "*"
        }
      ]
    """
``` 
